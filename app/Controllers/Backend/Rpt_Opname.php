<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_OpnameDetail;
use App\Models\M_Employee;
use Config\Services;

class Rpt_Opname extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_OpnameDetail($this->request);
    }

    public function index()
    {
        $start_date = date('Y-m-d', strtotime('- 1 days'));
        $start_created = date('Y-m-d');
        $end_date = date('Y-m-d');

        $role = $this->access->getUserRoleName($this->access->getSessionUser(), 'W_View_All_Data');

        $employee = null;

        if (empty($role)) {
            $mEmpl = new M_Employee($this->request);

            $employee = $mEmpl->where("sys_user_id", $this->access->getSessionUser())
                ->orderBy('name', 'ASC')
                ->first();
        }

        $data = [
            'date_created' => $start_created . ' - ' . $end_date,
            'employee' => $employee,
            'date_range' => $start_date . ' - ' . $end_date
        ];

        return $this->template->render('report/opname/v_opname', $data);
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
                $select = $this->model->getSelectOpname();
                $join = $this->model->getJoinOpname();
                $order = $this->request->getPost('columns');
                $sort = ['documentno' => 'ASC', 'opnamedate' => 'ASC'];
                $search = $this->request->getPost('search');

                $number = $this->request->getPost('start');
                $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search, $join);

                foreach ($list as $value) :
                    $row = [];

                    $row[] = $value->documentno;
                    $row[] = format_dmy($value->opnamedate, '-');
                    $row[] = $value->branch;
                    $row[] = $value->room;
                    $row[] = $value->employee;
                    $row[] = $value->assetcode;
                    $row[] = $value->product;
                    $row[] = active($value->check_branch);
                    $row[] = $value->branch_scan;
                    $row[] = active($value->check_room);
                    $row[] = $value->room_scan;
                    $row[] = active($value->check_employee);
                    $row[] = $value->employee_scan;
                    $row[] = active($value->isnew);
                    $row[] = $value->noc;
                    $row[] = $value->opnamer;
                    $row[] = date("d-m-Y H:i:s", strtotime($value->startdate));
                    $row[] = !empty($value->enddate) ? date("d-m-Y H:i:s", strtotime($value->enddate)) : "";
                    $row[] = docStatus($value->docstatus);
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
