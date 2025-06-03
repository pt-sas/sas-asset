<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Transaction;
use Config\Services;

class Rpt_HistoryAsset extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Transaction($this->request);
    }

    public function index()
    {
        return $this->template->render('report/assethistory/v_asset_history');
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
                $select = $this->model->getSelect();
                $join = $this->model->getJoin();
                $order = $this->request->getPost('columns');
                $sort = ['assetcode', 'ASC'];
                $search = $this->request->getPost('search');

                $number = $this->request->getPost('start');
                $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search, $join);

                foreach ($list as $value) :
                    $row = [];

                    $number++;

                    $row[] = $number;
                    $row[] = $value->assetcode;
                    $row[] = $value->product;
                    $row[] = date('d-m-Y', strtotime($value->transactiondate));
                    $row[] = $value->transactiontype;
                    $row[] = empty($value->description) ? $value->room : "{$value->room} ({$value->description})";
                    $row[] = $value->employee;
                    $row[] = $value->docreceipt;
                    $row[] = $value->docmovement;
                    $row[] = $value->created;

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
}
