<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_MovementDetail;
use App\Models\M_Status;
use Config\Services;

class Rpt_MovementDetail extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_MovementDetail($this->request);
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

        return $this->template->render('report/movement/v_movementdetail', $data);
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
                $select = $this->model->getMovementDetail();
                $join = $this->model->getJoinDetail();
                $order = $this->request->getPost('columns');
                $sort = ['documentno' => 'ASC'];
                $search = $this->request->getPost('search');
                $where['trx_movement.movementtype'] = $this->Movement_Kirim;

                $number = $this->request->getPost('start');
                $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search, $join, $where);

                foreach ($list as $value) :
                    $row = [];

                    $row[] = $value->assetcode;
                    $row[] = $value->documentno;
                    $row[] = format_dmy($value->movementdate, '-');
                    $row[] = $value->no_terima;
                    $row[] = !empty($value->tgl_terima) ? format_dmy($value->tgl_terima, '-') : "";
                    $row[] = $value->product;
                    $row[] = $value->branchfrom;
                    $row[] = $value->branchto;
                    $row[] = $value->divisionfrom;
                    $row[] = $value->divisionto;
                    $row[] = $value->employeefrom;
                    $row[] = $value->employeeto;
                    $row[] = $value->roomfrom;
                    $row[] = $value->roomto;
                    $row[] = $value->status;
                    $data[] = $row;
                endforeach;

                $recordTotal = $this->datatable->countAll($table, $select, $order, $sort, $search, $join, $where);
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
