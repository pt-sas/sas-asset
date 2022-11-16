<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Depreciation;
use Config\Services;

class Rpt_Depreciation extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Depreciation($this->request);
    }

    public function index()
    {
        return $this->template->render('report/depreciation/v_depreciation');
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
                $select = $this->model->findAll();
                $order = $this->request->getPost('columns');
                $sort = ['assetcode', 'ASC'];
                $search = $this->request->getPost('search');

                $number = $this->request->getPost('start');
                $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search);

                foreach ($list as $value) :
                    $row = [];

                    $row[] = $value->assetcode;
                    $row[] = format_dmy($value->transactiondate, '-');
                    $row[] = $value->totalyear;
                    $row[] = $value->startyear;
                    $row[] = formatRupiah($value->costdepreciation);
                    $row[] = formatRupiah($value->accumulateddepreciation);
                    $row[] = formatRupiah($value->bookvalue);
                    $row[] = $value->currentmonth;
                    $row[] = $value->depreciationtype === 'DB' ? 'Declining Balance' : 'Straight Line';

                    $data[] = $row;

                endforeach;

                $recordTotal = $this->datatable->countAll($table);
                $recordsFiltered = $this->datatable->countFiltered($table, $select, $order, $sort, $search);
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
