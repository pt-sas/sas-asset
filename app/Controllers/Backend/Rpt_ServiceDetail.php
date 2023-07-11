<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Service;
use App\Models\M_Status;
use Config\Services;

class Rpt_ServiceDetail extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Service($this->request);
    }

    public function index()
    {
        $start_date = date('Y-m-d', strtotime('- 1 days'));
        $end_date = date('Y-m-d');
        $status = new M_Status($this->request);


        $data = [
            'date_range' => $start_date . ' - ' . $end_date,

            'status'    => $status->where([
                'isactive'  => 'Y',
                'isline'      => 'Y'
            ])->like('menu_id', 'service')
                ->orderBy('name', 'ASC')
                ->findAll()
        ];

        return $this->template->render('report/service/v_reportservice', $data);
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
                $select = $this->model->getServiceDetail();
                $join = $this->model->getJoinDetail();
                $order = $this->request->getPost('columns');
                $sort = ['documentno', 'ASC'];
                $search = $this->request->getPost('search');

                $number = $this->request->getPost('start');
                $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search, $join);

                foreach ($list as $value) :
                    $row = [];

                    $row[] = $value->documentno;
                    $row[] = docStatus($value->docstatus);
                    $row[] = format_dmy($value->servicedate, '-');
                    $row[] = $value->supplier;
                    $row[] = $value->assetcode;
                    $row[] = $value->product;
                    $row[] = $value->status;

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
