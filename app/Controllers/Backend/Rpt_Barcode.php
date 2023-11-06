<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Barcode;
use App\Models\M_Inventory;
use Config\Services;

class Rpt_Barcode extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Inventory($this->request);
    }

    public function index()
    {
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');

        $data = [
            'date_range' => $start_date . ' - ' . $end_date
        ];

        return $this->template->render('report/barcode/v_barcode', $data);
    }

    public function showAll()
    {
        $post = $this->request->getVar();
        $data = [];

        $recordTotal = 0;
        $recordsFiltered = 0;

        if ($this->request->getMethod(true) === 'POST') {
            if (isset($post['form']) && $post['clear'] === 'false') {
                $table = $this->model->table;
                $select = $this->model->getSelectDetail();
                $join = $this->model->getJoinDetail();
                $order = $this->request->getPost('columns');
                $sort = $this->model->order;
                $search = $this->request->getPost('search');

                $number = $this->request->getPost('start');
                $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search, $join);

                foreach ($list as $value) :
                    $row = [];

                    $checkbox = '<div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input check-data" name="ischeck" value="' . $value->assetcode . '" checked>
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>';

                    $row[] = $checkbox;
                    $row[] = $value->assetcode;
                    $row[] = $value->employee;
                    $row[] = $value->room;
                    $row[] = $value->receipt;
                    $data[] = $row;

                endforeach;

                $recordTotal = $this->datatable->countAll($table, $select, $order, $sort, $search);
                $recordsFiltered = $this->datatable->countFiltered($table, $select, $order, $sort, $search, $join);
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


    public function print()
    {
        $cBarcode = new Barcode();

        $post = $this->request->getPost();
        $data = json_decode($post['assetcode']);

        $fileName = $cBarcode->getLabelAsset($data);
        $path = base_url('uploads') . '/' . $fileName;
        return json_encode($path);
    }
}
