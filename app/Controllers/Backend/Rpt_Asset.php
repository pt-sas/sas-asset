<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Inventory;
use Config\Services;

class Rpt_Asset extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Inventory($this->request);
    }

    public function index()
    {
        return $this->template->render('report/asset/v_asset');
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

                    $number++;

                    $row[] = $number;
                    $row[] = $value->assetcode;
                    $row[] = $value->groupasset;
                    $row[] = $value->brand;
                    $row[] = $value->category;
                    $row[] = $value->subcategory;
                    $row[] = $value->type;
                    $row[] = $value->product;
                    $row[] = $value->branch;
                    $row[] = $value->division;
                    $row[] = $value->room;
                    $row[] = $value->employee;
                    $data[] = $row;

                endforeach;

                $recordTotal = $this->datatable->countAll($table);
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
}
