<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Depreciation;
use App\Models\M_DepreciationDetail;
use App\Models\M_Inventory;
use Config\Services;

class Gen_Depreciation extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_DepreciationDetail($this->request);
    }

    public function index()
    {
        return $this->template->render('process/depreciation/v_depreciation');
    }

    public function showAll()
    {
        $depreciation = new M_Depreciation($this->request);

        $post = $this->request->getVar();
        $data = [];

        $recordTotal = 0;
        $recordsFiltered = 0;

        if ($this->request->getMethod(true) === 'POST') {
            if (isset($post['form']) && $post['clear'] === 'false') {
                $table = $this->model->table;
                $select = $this->model->getSelect();
                $join = $this->model->getJoin();
                $order = $this->request->getPost('columns');
                $sort = ['assetcode', 'ASC'];
                $search = $this->request->getPost('search');

                $number = $this->request->getPost('start');

                $assetCodes = $post['form'][0]['value'];

                if ($depreciation->whereIn('assetcode', $assetCodes)->delete()) {
                    $this->model->whereIn('assetcode', $assetCodes)->delete();
                    $t = $depreciation->createDepreciation($assetCodes);
                    // $inventory = new M_Inventory($this->request);

                    // $rowAsset = $inventory->whereIn('assetcode', $assetCodes)->findAll();

                    log_message('debug', 'Row Asset: ' . json_encode($t));

                    $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search, $join);

                    foreach ($list as $value) :
                        $row = [];

                        $row[] = $value->assetcode;
                        $row[] = $value->branch;
                        $row[] = $value->division;
                        $row[] = $value->room_name;
                        $row[] = $value->product;
                        $row[] = $value->employee;
                        $row[] = format_dmy($value->transactiondate, '-');
                        $row[] = formatRupiah($value->unitprice);
                        $row[] = $value->totalyear;
                        $row[] = $value->period;
                        $row[] = formatRupiah($value->residualvalue);
                        $row[] = formatRupiah($value->costdepreciation);
                        $row[] = formatRupiah($value->accumulateddepreciation);
                        $row[] = formatRupiah($value->bookvalue);
                        $row[] = $value->currentmonth;
                        $row[] = $value->depreciationtype;
                        $row[] = $value->sisa_waktu;

                        $data[] = $row;

                    endforeach;

                    $recordTotal = $this->datatable->countAll($table, $select, $order, $sort, $search);
                    $recordsFiltered = $this->datatable->countFiltered($table, $select, $order, $sort, $search, $join);
                }
            }

            $result = [
                'draw'              => $this->request->getPost('draw'),
                'recordsTotal'      => $recordTotal,
                'recordsFiltered'   => $recordsFiltered,
                'data'              => $data
            ];

            return $this->response->setJSON($result);
        }
    }
}
