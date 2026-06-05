<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

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

        //* Full month in one year 
        $fullMonth = 12;

        //* Cut off date
        $dateCO = 15;

        $arrData = [];
        foreach ($rowAsset as $key => $val) :
            $group = $groupasset->find($val->getGroupAssetId());

            //* Transaction Date 
            $dateTrx = $val->getInventoryDate();

            $strDate = strtotime($dateTrx);
            $currDate = date('d', $strDate);
            $currMonth = date('m', $strDate);

            //* Use Full Life from group asset
            $useFulLife = $group->getUsefulLife();
            $useLength = $useFulLife;

            //* book value of unitprice in inventory 
            $bookValue = $val->getUnitPrice();
            $residualValue = $val->getResidualValue();

            //* accumulated depreciation 
            $accumulation = 0;

            //? Check the date less than equal cut off date 
            if ($currDate <= $dateCO) {
                //? Check this month of january
                $notFullMonth = $currMonth == 01 ? $fullMonth : ($fullMonth - $currMonth) + 1;
                $remainMonth = ($fullMonth - $notFullMonth);
            }

            //? Check the date month than cut off date 
            if ($currDate > $dateCO) {
                $addMonth = strtotime("+1 months", $strDate);
                $nextMonth = date('m', $addMonth);

                //* Total month substract next month add current month to calculate 
                $notFullMonth = ($fullMonth - $nextMonth) + 1;
                $remainMonth = ($fullMonth - $notFullMonth);
            }

            if (!empty($remainMonth))
                $useLength = $useFulLife + 1;

            //TODO: Method Straight Line 
            $straightLine = (($bookValue - $residualValue) / $useFulLife);

            for ($i = 0; $i <= $useLength; $i++) {
                $row = [];
                $cost = 0;
                $currentMonth = 0;

                $year = date('Y', $strDate);

                //TODO: Method Double Decline
                $doubleLine = ((($bookValue - $residualValue) / $useFulLife) * 2);

                $isType = $group->getDepreciationType();

                //? Check method calculate depreciation
                $calculate = $isType === 'SL' ? $straightLine : $doubleLine;

                //* Index 1
                if ($i == 1) {
                    //? Check this month of december and date month than cut off date
                    if ($currMonth == 12 && $currDate > $dateCO) {
                        $addYear = addYear($dateTrx, $i);
                        $year = date('Y', $addYear);
                        $dateTrx = date('Y-m-d', $addYear);
                    }

                    $calculate *= ($notFullMonth / $fullMonth);

                    //* Set to check is not full month
                    $currentMonth = $notFullMonth;

                    $cost += $calculate;
                    $accumulation += $cost;
                    $bookValue -= $cost;
                    $row['bookvalue'] = round($bookValue, 2, PHP_ROUND_HALF_UP);
                    $row['costdepreciation'] = round($cost, 2, PHP_ROUND_HALF_UP);
                }

                //* Index greather than 1
                if ($i > 1) {
                    $increment = $i - 1;
                    $addYear = addYear($dateTrx, $increment);
                    $year = date('Y', $addYear);

                    //? Check current month if available remaining month
                    if (!empty($remainMonth) && $i == $useLength) {
                        $calculate *= ($remainMonth / $fullMonth);

                        //* Set remaining month 
                        $currentMonth = $remainMonth;
                    } else {
                        $calculate *= ($fullMonth / $fullMonth);

                        //* Set full month
                        $currentMonth = $fullMonth;
                    }

                    $cost += $calculate;
                    $accumulation += $cost;
                    $bookValue -= $cost;
                }

                $row['assetcode'] = $val->getAssetCode();
                $row['transactiondate'] = $val->getInventoryDate();
                $row['totalyear'] = $useFulLife;
                $row['startyear'] = $year;
                $row['residualvalue'] = round($residualValue, 2, PHP_ROUND_HALF_UP);
                $row['costdepreciation'] = round($cost, 2, PHP_ROUND_HALF_UP);
                $row['accumulateddepreciation'] = round($accumulation, 2, PHP_ROUND_HALF_UP);
                $row['bookvalue'] = round($bookValue, 2, PHP_ROUND_HALF_UP);
                $row['currentmonth'] = $currentMonth;
                $row['depreciationtype'] = $isType;
                $row['unitprice'] = $val->getUnitPrice();
                $row['created_by'] = session()->get('sys_user_id');
                $row['updated_by'] = session()->get('sys_user_id');
                $arrData[] = $row;
            }
        endforeach;

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
        $dateCO = 15;

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

                    if ($currDate > $dateCO)
                        $increment = $i;

                    if ($strDate !== strtotime($dateTrx) || $currDate <= $dateCO)
                        $increment = $i - 1;

                    $period = date("m", strtotime("+" . $increment . " months", $strDate));
                    $period = $startYear . "-" . $period;

                    $accumulation += $cost;

                    //? Check index first and strDate equal dateTrx or this month and cut off date
                    if ($i == 1 && $currentMonth != $currMonth && ($strDate == strtotime($dateTrx) || ($strDate == strtotime($dateTrx) && $currMonth == 01 && $currDate <= $dateCO) || $currMonth == 12 && $currDate > $dateCO))
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
