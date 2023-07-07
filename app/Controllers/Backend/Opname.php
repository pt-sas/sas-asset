<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Opname;
use App\Models\M_OpnameDetail;
use App\Models\M_Product;
use App\Models\M_Employee;
use App\Models\M_Branch;
use App\Models\M_Inventory;
use App\Models\M_Room;
use Config\Services;

class Opname extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Opname($this->request);
        $this->entity = new \App\Entities\Opname();
        $this->modelDetail = new M_OpnameDetail($this->request);
    }

    public function index()
    {
        $data = [
            'today'     => date('Y-m-d'),
            'startdate' => date("Y-m-d H:i:s"),
        ];

        return $this->template->render('transaction/opname/v_opname', $data);
    }

    public function showAll()
    {
        if ($this->request->getMethod(true) === 'POST') {
            $table = $this->model->table;
            $select = $this->model->getSelect();
            $join = $this->model->getJoin();
            $order = $this->model->column_order;
            $sort = $this->model->order;
            $search = $this->model->column_search;
            $where['trx_opname.created_by'] = $this->access->getSessionUser();

            $data = [];

            $number = $this->request->getPost('start');
            $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search, $join, $where);

            foreach ($list as $value) :
                $row = [];
                $ID = $value->trx_opname_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->documentno;
                $row[] = format_dmy($value->opnamedate, '-');
                $row[] = $value->branch;
                $row[] = $value->room;
                $row[] = $value->employee;
                $row[] = docStatus($value->docstatus);
                $row[] = $value->createdby;
                $row[] = $value->description;
                $row[] = $this->template->tableButton($ID, $value->docstatus);
                $data[] = $row;
            endforeach;

            $result = [
                'draw'              => $this->request->getPost('draw'),
                'recordsTotal'      => $this->datatable->countAll($table, $where),
                'recordsFiltered'   => $this->datatable->countFiltered($table, $select, $order, $sort, $search, $join, $where),
                'data'              => $data
            ];

            return $this->response->setJSON($result);
        }
    }

    public function create()
    {
        if ($this->request->getMethod(true) === 'POST') {
            $post = $this->request->getVar();

            $table = json_decode($post['table']);

            //! Mandatory property for detail validation
            $post['line'] = countLine($table);
            $post['detail'] = [
                'table' => arrTableLine($table)
            ];

            try {
                $this->entity->fill($post);
                $this->entity->setDocStatus($this->DOCSTATUS_Drafted);

                //* Insert data
                if ($this->isNew()) {
                    $docNo = $this->model->getInvNumber();
                    $this->entity->setDocumentNo($docNo);
                }

                if (!$this->validation->run($post, 'opname')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    $response = $this->save();
                }
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function show($id)
    {
        $mBranch = new M_Branch($this->request);
        $mRoom = new M_Room($this->request);
        $mEmployee = new M_Employee($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();
                $detail = $this->modelDetail->where($this->model->primaryKey, $id)->findAll();

                $rowBranch = $mBranch->find($list[0]->getBranchId());
                $list = $this->field->setDataSelect($mBranch->table, $list, $mBranch->primaryKey, $rowBranch->getBranchId(), $rowBranch->getName());

                $rowRoom = $mRoom->find($list[0]->getRoomId());
                $list = $this->field->setDataSelect($mRoom->table, $list, $mRoom->primaryKey, $rowRoom->getRoomId(), $rowRoom->getName());

                $rowEmployee = $mEmployee->find($list[0]->getEmployeeId());
                $list = $this->field->setDataSelect($mEmployee->table, $list, $mEmployee->primaryKey, $rowEmployee->getEmployeeId(), $rowEmployee->getName());

                $result = [
                    'header'    => $this->field->store($this->model->table, $list),
                    'line'      => $this->tableLine($id, $detail)
                ];

                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function destroy($id)
    {
        if ($this->request->isAJAX()) {
            try {
                $result = $this->model->delete($id);
                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function destroyLine($id)
    {
        if ($this->request->isAJAX()) {
            try {
                $delete = $this->modelDetail->delete($id);
                $response = message('success', true, $delete);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function processIt()
    {
        if ($this->request->isAJAX()) {
            $post = $this->request->getVar();

            $_ID = $post['id'];
            $_DocAction = $post['docaction'];

            try {
                $row = $this->model->find($_ID);

                if (!empty($_DocAction) && $row->getDocStatus() !== $_DocAction) {
                    $line = $this->modelDetail->where($this->model->primaryKey, $_ID)->first();

                    //? Exists data line or not exist data line and docstatus not Completed
                    if ($line || (!$line && $_DocAction !== $this->DOCSTATUS_Completed)) {
                        $this->entity->setDocStatus($_DocAction);
                    } else if (!$line && $_DocAction === $this->DOCSTATUS_Completed) {
                        $this->entity->setDocStatus($this->DOCSTATUS_Invalid);
                    }

                    $this->entity->setEndDate(date("Y-m-d H:i:s"));

                    $response = $this->save();
                } else if (empty($_DocAction)) {
                    $response = message('error', true, 'Please Choose the Document Action first');
                } else {
                    $response = message('error', true, 'Please reload the Document');
                }
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function getDetailAsset()
    {
        $mInv = new M_Inventory($this->request);

        if ($this->request->isAjax()) {
            $post = $this->request->getVar();

            try {
                $detail = $mInv->where('md_employee_id', $post['md_employee_id'])->findAll();

                $result = [
                    'line'      => $this->tableLine(null, $detail)
                ];

                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function tableLine($set = null, $detail = [])
    {
        $mInv = new M_Inventory($this->request);
        $mPro = new M_Product($this->request);
        $post = $this->request->getVar();

        $response = [];

        //* Data Product 
        $dataProduct = $mPro->where('isactive', 'Y')->findAll();

        //* Button Branch 
        $branchOk = '<button type="button" class="btn btn-link btn-success line" id="Y" name="isbranch">
        <i class="fa fa-check fa-2x"></i></button>';
        $branchNotOk = '<button type="button" class="btn btn-link btn-danger line" id="N" name="isbranch">
        <i class="fa fa-times fa-2x"></i></button>';

        //* Button Room 
        $roomOk = '<button type="button" class="btn btn-link btn-success line" id="Y" name="isroom">
        <i class="fa fa-check fa-2x"></i></button>';
        $roomNotOk = '<button type="button" class="btn btn-link btn-danger line" id="N" name="isroom">
        <i class="fa fa-times fa-2x"></i></button>';

        //* Button Employee 
        $employeeOk = '<button type="button" class="btn btn-link btn-success line" id="Y" name="isemployee">
        <i class="fa fa-check fa-2x"></i></button>';
        $employeeNotOk = '<button type="button" class="btn btn-link btn-danger line" id="N" name="isemployee">
        <i class="fa fa-times fa-2x"></i></button>';

        $new = '<span class="badge badge-success" id="Y" name="isnew">Yes</span>';
        $notNew = '<span class="badge badge-danger" id="N" name="isnew">No</span>';

        $numberOfCheck = 0;

        if ($this->request->getMethod(true) === 'POST') {
            //? Create
            if (empty($set) && count($detail) > 0) {
                foreach ($detail as $row) :
                    $response[] = [
                        $this->field->fieldTable('input', 'text', 'assetcode', 'text-uppercase unique', null, 'readonly', null, null, $row->assetcode, 170),
                        $this->field->fieldTable('select', null, 'md_product_id', null, null, 'readonly', null, $dataProduct, $row->md_product_id, 500, 'md_product_id', 'name'),
                        null,
                        null,
                        null,
                        $notNew,
                        $numberOfCheck,
                        null
                    ];
                endforeach;
            } else if (!empty($post['scan_assetcode'])) {
                if (!$this->validation->run($post, 'opname_scan')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    $row = $mInv->where('assetcode', $post['scan_assetcode'])->first();

                    if ($row) {
                        if (isset($post['noc'])) {
                            if ($post['noc'] == 0)
                                $numberOfCheck++;
                            else
                                $numberOfCheck = $post['noc'];
                        }

                        if ($post['status'] === 'true') {
                            $data = [
                                'edit' => [
                                    'isbranch'      => $row->md_branch_id == $post['md_branch_id'] ? $branchOk : $branchNotOk,
                                    'isroom'        => $row->md_room_id == $post['md_room_id'] ? $roomOk : $roomNotOk,
                                    'isemployee'    => $row->md_employee_id == $post['md_employee_id'] ? $employeeOk : $employeeNotOk,
                                    'isnew'         => $row->md_employee_id == $post['md_employee_id'] ? $notNew : $new,
                                    'nocheck'       => $numberOfCheck
                                ]
                            ];
                        } else {
                            $numberOfCheck++;

                            $data = [
                                'new' => [
                                    $this->field->fieldTable('input', 'text', 'assetcode', 'text-uppercase unique', null, 'readonly', null, null, $row->assetcode, 170),
                                    $this->field->fieldTable('select', null, 'md_product_id', null, null, 'readonly', null, $dataProduct, $row->md_product_id, 500, 'md_product_id', 'name'),
                                    $row->md_branch_id == $post['md_branch_id'] ? $branchOk : $branchNotOk,
                                    $row->md_room_id == $post['md_room_id'] ? $roomOk : $roomNotOk,
                                    $row->md_employee_id == $post['md_employee_id'] ? $employeeOk : $employeeNotOk,
                                    $new,
                                    $numberOfCheck,
                                    $this->field->fieldTable('button', 'button', 'trx_opname_detail_id'),
                                ]
                            ];
                        }

                        $response = message('success', true, $data);
                    } else {
                        $msg = 'Asset Code does not exist';
                        $response = message('success', false, $msg);
                    }
                }
            }
        }

        //? Update
        if (!empty($set) && count($detail) > 0) {
            foreach ($detail as $row) :
                $response[] = [
                    $this->field->fieldTable('input', 'text', 'assetcode', 'text-uppercase unique', null, 'readonly', null, null, $row->assetcode, 170),
                    $this->field->fieldTable('select', null, 'md_product_id', null, null, 'readonly', null, $dataProduct, $row->md_product_id, 500, 'md_product_id', 'name'),
                    $row->isbranch === "Y" ? $branchOk : $branchNotOk,
                    $row->isroom === "Y" ? $roomOk : $roomNotOk,
                    $row->isemployee === "Y" ? $employeeOk : $employeeNotOk,
                    $row->isnew === "Y" ? $new : $notNew,
                    $row->nocheck,
                    $row->isnew === "Y" ? $this->field->fieldTable('button', 'button', 'trx_opname_detail_id', null, null, null, null, null, $row->trx_opname_detail_id) : null,
                ];
            endforeach;
        }

        return json_encode($response);
    }
}
