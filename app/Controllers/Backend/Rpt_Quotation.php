<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Quotation;
use Config\Services;

class Rpt_Quotation extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Quotation($this->request);
    }

    public function index()
    {
        $start_date = date('Y-m-d', strtotime('- 1 days'));
        $end_date = date('Y-m-d');

        $data = [
            'date_range' => $start_date . ' - ' . $end_date
        ];

        return $this->template->render('report/quotation/v_quotation', $data);
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
                $select = $this->model->getSelectReport();
                $join = $this->model->getJoinReport();
                $order = $this->request->getPost('columns');
                $sort = $this->model->order;
                $search = $this->request->getPost('search');
                $where['trx_quotation.isinternaluse'] = 'N';

                $number = $this->request->getPost('start');
                $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search, $join, $where);

                foreach ($list as $value) :
                    $row = [];

                    $number++;

                    $row[] = $number;
                    $row[] = $value->documentno;
                    $row[] = $value->supplier;
                    $row[] = $value->quotationdate;
                    $row[] = $value->docstatus;
                    $row[] = $value->status;
                    $row[] = $value->product;
                    $row[] = $value->qtyentered;
                    $row[] = $value->qtyreceipt;
                    $row[] = $value->unitprice;
                    $row[] = $value->lineamt;
                    $row[] = $value->employee;

                    $data[] = $row;
                endforeach;

                $recordTotal = $this->datatable->countAll($table);
                $recordsFiltered = $this->datatable->countFiltered($table, $select, $order, $sort, $search, $join, $where);
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
