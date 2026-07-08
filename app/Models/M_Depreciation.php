<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;
use DateTime;

class M_Depreciation extends Model
{
    protected $table            = 'trx_depreciation';
    protected $primaryKey       = 'trx_depreciation_id';
    protected $allowedFields    = [
        'assetcode',
        'transactiondate',
        'totalyear',
        'startyear',
        'residualvalue',
        'costdepreciation',
        'accumulateddepreciation',
        'bookvalue',
        'currentmonth',
        'depreciationtype',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps    = true;
    protected $returnType       = 'App\Entities\Depreciation';
    protected $request;
    protected $db;
    protected $builder;

    public function __construct(RequestInterface $request)
    {
        parent::__construct();
        $this->db = db_connect();
        $this->request = $request;
        $this->builder = $this->db->table($this->table);
    }

    public function doInsert(array $data)
    {
        if (!is_array($data))
            return false;

        if (empty($data))
            return false;

        if (is_array($data)) {
            $result = $this->builder->insertBatch($data);

            return $result > 0 ? true : false;
        }

        return false;
    }

    public function getSelect()
    {
        $sql = $this->table . '.*,
        sys_ref_detail.name as depreciationtype,
        md_product.name as product';

        return $sql;
    }

    public function getJoin()
    {
        //* DepreciationType
        $defaultID = 9;

        $sql = [
            $this->setDataJoin('sys_ref_detail', 'sys_ref_detail.sys_reference_id = ' . $defaultID . ' AND sys_ref_detail.value = ' . $this->table . '.depreciationtype', 'left'),
            $this->setDataJoin('trx_inventory', 'trx_inventory.assetcode = ' . $this->table . '.assetcode', 'left'),
            $this->setDataJoin('md_product', 'md_product.md_product_id = trx_inventory.md_product_id', 'left'),
        ];

        return $sql;
    }

    private function setDataJoin($tableJoin, $columnJoin, $typeJoin = "inner")
    {
        return [
            "tableJoin" => $tableJoin,
            "columnJoin" => $columnJoin,
            "typeJoin" => $typeJoin
        ];
    }

    /**
     * Process Create data on the table Depreciation
     *
     * @param [type] $data
     * @return void
     */
    public function createDepreciation($assetCodes)
    {
        $inventory = new M_Inventory($this->request);
        $groupasset = new M_GroupAsset($this->request);
        $depreciation = new M_Depreciation($this->request);
        $depreciationDetail = new M_DepreciationDetail($this->request);

        $rowAsset = $inventory->whereIn('assetcode', $assetCodes)->findAll();

        $fullMonth = 12;
        $cutOffDate = 15;

        $arrData = [];

        foreach ($rowAsset as $val) {

            $group = $groupasset->find($val->getGroupAssetId());

            $dateTrx = $val->getInventoryDate();

            $startDate = new DateTime($dateTrx);

            // Tentukan mulai depresiasi
            if ((int)$startDate->format('d') > $cutOffDate) {
                $startDate->modify('first day of next month');
            } else {
                $startDate->modify('first day of this month');
            }

            $startYear  = (int)$startDate->format('Y');
            $startMonth = (int)$startDate->format('n');

            $usefulLife = $group->getUsefulLife();
            $useLength  = $usefulLife;

            $bookValue      = $val->getUnitPrice();
            $residualValue  = $val->getResidualValue();
            $accumulation   = 0;

            // Bulan pertama
            $notFullMonth = 13 - $startMonth;

            // Bulan terakhir
            $remainMonth = $fullMonth - $notFullMonth;

            if ($remainMonth > 0) {
                $useLength++;
            }

            $straightLine = ($bookValue - $residualValue) / $usefulLife;

            for ($i = 0; $i <= $useLength; $i++) {

                $row = [];

                $cost = 0;
                $currentMonth = 0;

                $tmpDate = clone $startDate;
                $tmpDate->modify("+{$i} year");

                $year = $tmpDate->format('Y');

                $doubleLine = (($bookValue - $residualValue) / $usefulLife) * 2;

                $isType = $group->getDepreciationType();

                $calculate = ($isType === 'SL')
                    ? $straightLine
                    : $doubleLine;

                if ($i == 0) {

                    $calculate *= ($notFullMonth / $fullMonth);

                    $currentMonth = $notFullMonth;
                } else {

                    if ($remainMonth > 0 && $i == $useLength) {

                        $calculate *= ($remainMonth / $fullMonth);

                        $currentMonth = $remainMonth;
                    } else {

                        $currentMonth = $fullMonth;
                    }
                }

                $cost = $calculate;

                $accumulation += $cost;

                $bookValue -= $cost;

                $row['assetcode'] = $val->getAssetCode();
                $row['transactiondate'] = $dateTrx;
                $row['totalyear'] = $usefulLife;
                $row['startyear'] = $year;
                $row['residualvalue'] = round($residualValue, 2);
                $row['costdepreciation'] = round($cost, 2);
                $row['accumulateddepreciation'] = round($accumulation, 2);
                $row['bookvalue'] = round($bookValue, 2);
                $row['currentmonth'] = $currentMonth;
                $row['depreciationtype'] = $isType;
                $row['unitprice'] = $val->getUnitPrice();
                $row['created_by'] = $this->access->getSessionUser();
                $row['updated_by'] = $this->access->getSessionUser();

                $arrData[] = $row;
            }
        }

        $arrDetail = $this->createDepreceiationMonth($arrData);

        $arrData = $this->doStripLine($arrData, $depreciation);
        $arrDetail = $this->doStripLine($arrDetail, $depreciationDetail);

        $depreciation->db->transBegin();

        try {
            //* Insert Table Depreciation 
            $depreciation->doInsert($arrData);

            //* Insert Table Depreciation Detail
            $result = $depreciationDetail->doInsert($arrDetail);

            $depreciation->db->transCommit();
        } catch (\Exception $e) {
            $depreciation->db->transRollback();
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return $result;
    }

    protected function createDepreceiationMonth($data)
    {
        //* Full month in one year 
        $fullMonth = 12;

        //* Cut off date
        $cutOffDate = 15;

        $arrDetail = [];
        foreach ($data as $key => $val) :
            //* Transaction Date 
            $dateTrx = $val['transactiondate'];
            $strDate = strtotime($dateTrx);
            $currDate = date('d', $strDate);
            $currMonth = date('m', $strDate);
            $yearMonth = date('Y', $strDate);

            $assetCode = $val['assetcode'];
            $startYear = $val['startyear'];
            $totalYear = $val['totalyear'];
            $currentMonth = $val['currentmonth'];
            $accumulation = $val['accumulateddepreciation'];
            $cost = $val['costdepreciation'];
            $type = $val['depreciationtype'];
            $residu = $val['residualvalue'];

            if ($currentMonth != 0) {
                $i = 1;
                $row = [];

                //* accumulated depreciation 
                if ($i == 1)
                    $accumulation = ($accumulation - $cost);

                $cost = ($cost / $currentMonth);

                if ($currentMonth != $fullMonth) {
                    if ($startYear != $yearMonth) {
                        $year = $startYear . "-01-01";
                        $strDate = strtotime($year);
                    }
                } else {
                    $year = $startYear . "-01-01";
                    $strDate = strtotime($year);
                }

                for ($i; $i <= $currentMonth; $i++) {
                    $bookValue = $val['unitprice'];

                    if ($currDate > $cutOffDate)
                        $increment = $i;

                    if ($strDate !== strtotime($dateTrx) || $currDate <= $cutOffDate)
                        $increment = $i - 1;

                    $date = date('Y-m-01', $strDate);
                    $period = date('Y-m', strtotime("+{$increment} month", strtotime($date)));

                    $accumulation += $cost;

                    //? Check index first and strDate equal dateTrx or this month and cut off date
                    if ($i == 1 && $currentMonth != $currMonth && ($strDate == strtotime($dateTrx) || ($strDate == strtotime($dateTrx) && $currMonth == 01 && $currDate <= $cutOffDate) || $currMonth == 12 && $currDate > $cutOffDate))
                        $accumulation = $cost + 0;

                    $bookValue -= $accumulation;

                    $row['assetcode'] = $assetCode;
                    $row['transactiondate'] = $dateTrx;
                    $row['totalyear'] = $totalYear;
                    $row['period'] = $period;
                    $row['residualvalue'] = round($residu, 2, PHP_ROUND_HALF_UP);
                    $row['costdepreciation'] = round($cost, 2, PHP_ROUND_HALF_UP);
                    $row['accumulateddepreciation'] = round($accumulation, 2, PHP_ROUND_HALF_UP);
                    $row['bookvalue'] = round($bookValue, 2, PHP_ROUND_HALF_UP);
                    $row['depreciationtype'] = $type;
                    $row['currentmonth'] = $currentMonth;
                    $row['created_by'] = $val['created_by'];
                    $row['updated_by'] = $val['updated_by'];
                    $arrDetail[] = $row;
                }
            }
        endforeach;

        return $arrDetail;
    }

    /**
     * Ensures that only the fields that are allowed to be updated
     * are in the data array table line.
     *
     * @param array $data
     * @return array
     */
    protected function doStripLine(array $data, $model): array
    {
        $result = [];

        if (!is_null($model) && is_object($model))
            $modelDetail = $model;

        foreach ($data as $value) :
            $data = (array) $value;

            foreach (array_keys($data) as $key) :
                if (!in_array($key, $modelDetail->allowedFields, true) && $key !== $modelDetail->primaryKey)
                    unset($data[$key]);
            endforeach;

            $result[] = $data;
        endforeach;

        return $result;
    }
}
