<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Branch;
use App\Models\M_Employee;
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
        $mEmpl = new M_Employee($this->request);
        $mBranch = new M_Branch($this->request);

        $data = [];

        $employee = $mEmpl->where("sys_user_id", $this->access->getSessionUser())->first();
        $branch = $mBranch->where("md_branch_id", $employee->getBranchId())->first();
        $roleViewAll = $this->access->getUserRoleName($this->access->getSessionUser(), 'W_View_All_Data');
        $roleViewMgrAll = $this->access->getUserRoleName($this->access->getSessionUser(), 'W_View_All_Mgr_Data');

        if (empty($roleViewAll) && empty($roleViewMgrAll))
            $data["employee"] = $employee;

        if (empty($roleViewAll) && $roleViewMgrAll)
            $data["branch"] = $branch;

        return $this->template->render('report/asset/v_asset', $data);
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
                    $row[] = $value->numberplate;
                    $row[] = $value->groupasset;
                    $row[] = $value->brand;
                    $row[] = $value->category;
                    $row[] = $value->subcategory;
                    $row[] = $value->type;
                    $row[] = $value->product;
                    $row[] = $value->branch;
                    $row[] = $value->division;
                    $row[] = $value->room;
                    $row[] = $value->description;
                    $row[] = $value->employee;
                    $row[] = active($value->isspare);
                    $data[] = $row;

                endforeach;

                $recordTotal = $this->datatable->countAll($table, $select, $order, $sort, $search);
                $recordsFiltered = $this->datatable->countFiltered($table, $select, $order, $sort, $search, $join);
            }

            $result = [
                'draw'              => $this->request->getPost('draw'),
                'recordsTotal'      => $recordTotal,
                'recordsFiltered'   => $recordsFiltered,
                'data'              => $data,
                'form'              => $post
            ];

            return $this->response->setJSON($result);
        }
    }
}
