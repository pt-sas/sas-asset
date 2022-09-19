<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Movement;
use App\Models\M_MovementDetail;
use App\Models\M_Product;
use App\Models\M_Employee;
use App\Models\M_Division;
use App\Models\M_Branch;
use App\Models\M_Room;
use App\Models\M_Inventory;
use App\Models\M_Status;
use App\Models\M_Transaction;
use Config\Services;
use stdClass;

class Movement extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Movement($this->request);
        $this->entity = new \App\Entities\Movement();
    }

    public function index()
    {
        $data = [
            'today'         => date('Y-m-d')
        ];

        return $this->template->render('transaction/movement/v_movement', $data);
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
            $where = [];

            //? Check is user exist role W_View_All_Movement 
            if (!$this->access->getUserRoleName($this->access->getSessionUser(), 'W_View_All_Movement')) {
                $where['trx_movement.created_by'] = $this->access->getSessionUser();
            }

            $data = [];

            $number = $this->request->getPost('start');
            $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search, $join, $where);

            foreach ($list as $value) :
                $row = [];
                $ID = $value->trx_movement_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->documentno;
                $row[] = format_dmy($value->movementdate, '-');
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

            //* Mandatory property for detail validation
            $post['line'] = countLine(count($table));
            $post['detail'] = [
                'table' => arrTableLine($table)
            ];

            try {
                $this->entity->fill($post);
                $this->entity->setDocStatus($this->DOCSTATUS_Drafted);

                if (!$this->validation->run($post, 'movement')) {
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
        $moveDetail = new M_MovementDetail();

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();
                $detail = $moveDetail->detail($this->model->table . '.' . $this->model->primaryKey, $id)->getResult();

                $result = [
                    'header'    => $this->field->store($this->model->table, $list),
                    'line'      => $this->tableLine('edit', $detail)
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

    public function processIt()
    {
        $moveDetail = new M_MovementDetail();
        $inventory = new M_Inventory($this->request);
        $transaction = new M_Transaction();

        if ($this->request->isAJAX()) {
            $_ID = $this->request->getGet('id');
            $_DocAction = $this->request->getGet('docaction');

            $row = $this->model->find($_ID);

            $line = $moveDetail->where($this->model->primaryKey, $_ID)->findAll();

            try {
                if (!empty($_DocAction) && $row->docstatus !== $_DocAction) {
                    //* condition exist data line or not exist data line and docstatus not Completed
                    if (count($line) > 0 || (count($line) == 0 && $_DocAction !== $this->DOCSTATUS_Completed)) {
                        $this->entity->setDocStatus($_DocAction);
                    } else if (count($line) == 0 && $_DocAction === $this->DOCSTATUS_Completed) {
                        $this->entity->setDocStatus($this->DOCSTATUS_Invalid);
                    }

                    $response = $this->save();

                    //* condition exist data line and docstatus Completed
                    if (count($line) > 0 && $_DocAction === $this->DOCSTATUS_Completed) {
                        //* Passing data to table inventory
                        $inventory->edit($line);

                        //* Passing data to table transaction
                        $arrMoveIn = [];
                        $arrMoveOut = [];
                        foreach ($line as $key => $value) :
                            //? Data movement from
                            $arrOut = new stdClass();
                            $arrOut->assetcode = $value->assetcode;
                            $arrOut->md_product_id = $value->md_product_id;
                            $arrOut->md_employee_id = $value->employee_from;
                            $arrOut->md_room_id = $value->room_from;
                            $arrOut->transactiontype = $this->Movement_Out;
                            $arrOut->transactiondate = $row->movementdate;
                            $arrOut->qtyentered = -1;
                            $arrOut->trx_movement_detail_id = $value->trx_movement_detail_id;
                            $arrMoveOut[$key] = $arrOut;

                            //? Data movement to
                            $arrIn = new stdClass();
                            $arrIn->assetcode = $value->assetcode;
                            $arrIn->md_product_id = $value->md_product_id;
                            $arrIn->md_employee_id = $value->employee_to;
                            $arrIn->md_room_id = $value->room_to;
                            $arrIn->transactiontype = $this->Movement_In;
                            $arrIn->transactiondate = $row->movementdate;
                            $arrIn->qtyentered = 1;
                            $arrIn->trx_movement_detail_id = $value->trx_movement_detail_id;
                            $arrMoveIn[$key] = $arrIn;
                        endforeach;

                        $arrData = (array) array_merge(
                            (array) $arrMoveIn,
                            (array) $arrMoveOut
                        );

                        $transaction->create($arrData);
                    }
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

    public function destroyLine($id)
    {
        $moveDetail = new M_MovementDetail();

        if ($this->request->isAJAX()) {
            try {
                $result = $moveDetail->delete($id);
                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function getSeqCode()
    {
        if ($this->request->isAJAX()) {
            try {
                $docNo = $this->model->getInvNumber();
                $response = message('success', true, $docNo);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function tableLine($set = null, $detail = [])
    {
        $uri = $this->request->uri->getSegment(2);
        $product = new M_Product($this->request);
        $employee = new M_Employee($this->request);
        $division = new M_Division($this->request);
        $branch = new M_Branch($this->request);
        $room = new M_Room($this->request);
        $inventory = new M_Inventory($this->request);
        $status = new M_Status($this->request);

        $role = $this->access->getUserRoleName($this->access->getSessionUser(), 'W_View_All_Movement');

        //TODO:  Check Data Employee based on sys_user_id
        $rowEmp = $employee->where('sys_user_id', $this->access->getSessionUser())->first();

        //? Not Exists Role W_View_All_Movement and exists data employee
        if (!$role && $rowEmp) {
            //? Where clause inventory 
            $invWhere['md_employee_id'] = $rowEmp->getEmployeeId();

            //? Where clause employee to 
            $empWhere['md_employee_id <>'] = $rowEmp->getEmployeeId();
            $empWhere['md_division_id'] = $rowEmp->getDivisionId();
        }

        //? Where Clause Inventory room bukan RUANG IT - BARANG RUSAK
        $invWhere['isactive'] = 'Y';
        $invWhere['md_room_id <>'] = 100041;

        //* Data Inventory 
        $dataInventory = $inventory->where($invWhere)->orderBy('assetcode', 'ASC')->findAll();

        //* Data Product 
        $dataProduct = $product->where('isactive', 'Y')->findAll();

        //* Data Employee From 
        $dataEmployee = $employee->where('isactive', 'Y')->findAll();

        //? Where Clause Employee 
        $empWhere['isactive'] = 'Y';

        //* Data Employee To 
        $dataEmployeeTo = $employee->where($empWhere)->findAll();

        //* Data Division
        $dataDivision = $division->where('isactive', 'Y')->findAll();

        //* Data Branch
        $dataBranch = $branch->where('isactive', 'Y')->findAll();

        //* Data Room
        $dataRoom = $room->where('isactive', 'Y')->findAll();

        //* Data Status
        $dataStatus = $status->where([
            'isactive'  => 'Y',
            'isline'    => 'Y'
        ])->like('menu_id', $uri)
            ->orderBy('name', 'ASC')
            ->findAll();

        $table = [];

        //? Create
        if (empty($set)) {
            $table = [
                $this->field->fieldTable('select', null, 'assetcode', 'unique', 'required', null, null, $dataInventory, null, 170, 'assetcode', 'assetcode'),
                $this->field->fieldTable('select', null, 'product_id', null, null, 'readonly', null, $dataProduct, null, 300, 'md_product_id', 'name'),
                $this->field->fieldTable('select', null, 'status_id', null, 'required', null, null, $dataStatus, 'BAGUS', 150, 'md_status_id', 'name'),
                $this->field->fieldTable('select', null, 'employee_from', null, null, 'readonly', null, $dataEmployee, null, 200, 'md_employee_id', 'name'),
                $this->field->fieldTable('select', null, 'employee_to', null, 'required', null, null, $dataEmployeeTo, null, 200, 'md_employee_id', 'name'),
                $this->field->fieldTable('select', null, 'branch_from', null, null, 'readonly', null, $dataBranch, null, 200, 'md_branch_id', 'name'),
                $this->field->fieldTable('select', null, 'branch_to', null, null, 'readonly', null, null, null, 200),
                $this->field->fieldTable('select', null, 'division_from', null, null, 'readonly', null, $dataDivision, null, 200, 'md_division_id', 'name'),
                $this->field->fieldTable('select', null, 'division_to', null, null, 'readonly', null, null, null, 200),
                $this->field->fieldTable('select', null, 'room_from', null, null, 'readonly', null, $dataRoom, null, 250, 'md_room_id', 'name'),
                $this->field->fieldTable('select', null, 'room_to', null, 'required', null, null, null, null, 250),
                $this->field->fieldTable('input', 'text', 'desc', null, null, null, null, null, null, 250),
                $this->field->fieldTable('button', 'button', 'delete')
            ];
        }

        //? Update
        if (!empty($set) && count($detail) > 0) {
            foreach ($detail as $row) :
                if ($row->docstatus == $this->DOCSTATUS_Completed) {
                    $dataInventory = $inventory->findAll();
                }

                $table[] = [
                    $this->field->fieldTable('select', null, 'assetcode', 'unique', 'required', null, null, $dataInventory, $row->assetcode, 170, 'assetcode', 'assetcode'),
                    $this->field->fieldTable('select', null, 'product_id', null, null, 'readonly', null, $dataProduct, $row->md_product_id, 300, 'md_product_id', 'name'),
                    $this->field->fieldTable('select', null, 'status_id', null, 'required', null, null, $dataStatus, $row->md_status_id, 150, 'md_status_id', 'name'),
                    $this->field->fieldTable('select', null, 'employee_from', null, null, 'readonly', null, $dataEmployee, $row->employee_from, 200, 'md_employee_id', 'name'),
                    $this->field->fieldTable('select', null, 'employee_to', null, 'required', $row->status === 'RUSAK' ? 'readonly' : null, null, $dataEmployeeTo, $row->employee_to, 200, 'md_employee_id', 'name'),
                    $this->field->fieldTable('select', null, 'branch_from', null, null, 'readonly', null, $dataBranch, $row->branch_from, 200, 'md_branch_id', 'name'),
                    $this->field->fieldTable('select', null, 'branch_to', null, null, 'readonly', null, $dataBranch, $row->branch_to, 200, 'md_branch_id', 'name'),
                    $this->field->fieldTable('select', null, 'division_from', null, null, 'readonly', null, $dataDivision, $row->division_from, 200, 'md_division_id', 'name'),
                    $this->field->fieldTable('select', null, 'division_to', null, null, 'readonly', null, $dataDivision, $row->division_to, 200, 'md_division_id', 'name'),
                    $this->field->fieldTable('select', null, 'room_from', null, null, 'readonly', null, $dataRoom, $row->room_from, 250, 'md_room_id', 'name'),
                    $this->field->fieldTable('select', null, 'room_to', null, 'required', null, null, $dataRoom, $row->room_to, 250, 'md_room_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'desc', null, null, null, null, null, $row->description, 250),
                    $this->field->fieldTable('button', 'button', 'delete', null, null, null, null, null, $row->trx_movement_detail_id)
                ];
            endforeach;
        }

        return json_encode($table);
    }
}
