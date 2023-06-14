<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Movement;
use App\Models\M_MovementDetail;
use App\Models\M_Product;
use App\Models\M_Employee;
use App\Models\M_Division;
use App\Models\M_Branch;
use App\Models\M_ChangeLog;
use App\Models\M_Room;
use App\Models\M_Inventory;
use App\Models\M_Status;
use App\Models\M_Transaction;
use App\Models\M_Reference;
use App\Models\M_WActivity;
use App\Models\M_WEvent;
use Config\Services;
use stdClass;
use Pusher\Pusher;

class Movement extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Movement($this->request);
        $this->modelDetail = new M_MovementDetail($this->request);
        $this->entity = new \App\Entities\Movement();
    }

    public function index()
    {
        $reference = new M_Reference($this->request);

        $data = [
            'today'         => date('Y-m-d'),
            'ref_list'      => $reference->findBy([
                'sys_reference.name'              => 'MovementType',
                'sys_reference.isactive'          => 'Y',
                'sys_ref_detail.isactive'         => 'Y',
            ], null, [
                'field'     => 'sys_ref_detail.name',
                'option'    => 'ASC'
            ])->getResult()
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
            // if (!$this->access->getUserRoleName($this->access->getSessionUser(), 'W_View_All_Movement')) {
            //     $where['trx_movement.created_by'] = $this->access->getSessionUser();
            // }

            $data = [];

            $number = $this->request->getPost('start');
            $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search, $join, $where);

            foreach ($list as $value) :
                $row = [];
                $ID = $value->trx_movement_id;

                //* Total aset movement 
                $totalLine = $this->modelDetail->where([
                    $this->model->primaryKey    => $ID
                ])->countAllResults();

                //* Aset movement have been received
                $avaiLine = $this->modelDetail->where([
                    $this->model->primaryKey    => $ID,
                    "isaccept"                  => "Y"
                ])->countAllResults();

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->documentno;
                $row[] = format_dmy($value->movementdate, '-');
                $row[] = $value->movementtype;
                $row[] = $value->referenceno;
                $row[] = $value->branch;
                $row[] = $value->branchto;
                $row[] = $value->division;
                $row[] = docStatus($value->docstatus, $value->movementtype, $totalLine, $avaiLine);
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
                    $docNo = $this->model->getInvNumber($post['movementtype']);
                    $this->entity->setDocumentNo($docNo);
                }

                if (!$this->validation->run($post, 'movement')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    $response = $this->save();
                }
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            // return $this->response->setJSON($response);
            return json_encode($response);
        }
    }

    public function show($id)
    {
        $branch = new M_Branch($this->request);
        $division = new M_Division($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();
                $detail = $this->modelDetail->where($this->model->primaryKey, $id)->findAll();

                if (!empty($list[0]->getRefMovementId())) {
                    $rowRefMove = $this->model->find($list[0]->getRefMovementId());
                    $list = $this->field->setDataSelect($this->model->table, $list, "ref_movement_id", $rowRefMove->getRefMovementId(), $rowRefMove->getDocumentNo());
                }

                $rowBranch = $branch->find($list[0]->getBranchId());
                $rowDivision = $division->find($list[0]->getDivisionId());

                $list = $this->field->setDataSelect($branch->table, $list, $branch->primaryKey, $rowBranch->getBranchId(), $rowBranch->getName());
                $list = $this->field->setDataSelect($division->table, $list, $division->primaryKey, $rowDivision->getDivisionId(), $rowDivision->getName());

                if (!empty($list[0]->getBranchToId())) {
                    $rowBranchTo = $branch->find($list[0]->getBranchToId());
                    $list = $this->field->setDataSelect($branch->table, $list, "md_branchto_id", $rowBranchTo->getBranchId(), $rowBranchTo->getName());
                }

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
        $inventory = new M_Inventory($this->request);
        $transaction = new M_Transaction();

        if ($this->request->isAJAX()) {
            $_ID = $this->request->getGet('id');
            $_DocAction = $this->request->getGet('docaction');

            $row = $this->model->find($_ID);

            $line = $this->modelDetail->where($this->model->primaryKey, $_ID)->findAll();

            try {
                if (!empty($_DocAction) && $row->docstatus !== $_DocAction) {
                    //* condition exist data line or not exist data line and docstatus not Completed
                    if (count($line) > 0 || (count($line) == 0 && $_DocAction !== $this->DOCSTATUS_Completed)) {
                        $this->entity->setDocStatus($_DocAction);
                        $response = $this->save();

                        if ($_DocAction === $this->DOCSTATUS_Completed) {
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
                                $arrOut->transactiondate = date("Y-m-d");
                                $arrOut->qtyentered = -1;
                                $arrOut->trx_movement_detail_id = $value->trx_movement_detail_id;
                                $arrMoveOut[$key] = $arrOut;

                                //? Data movement to
                                $arrIn = new stdClass();
                                $room = new M_Room($this->request);
                                $transit = $room->where("name", "TRANSIT")->first();

                                $arrIn->assetcode = $value->assetcode;
                                $arrIn->md_product_id = $value->md_product_id;
                                $arrIn->md_employee_id = $value->employee_to;
                                $arrIn->md_branch_id = $value->md_branch_id;
                                $arrIn->md_division_id = $value->md_division_id;
                                $arrIn->md_room_id = $transit->md_room_id;
                                $arrIn->transactiontype = $this->Movement_In;
                                $arrIn->transactiondate = date("Y-m-d");
                                $arrIn->qtyentered = 1;
                                $arrIn->trx_movement_detail_id = $value->trx_movement_detail_id;
                                $arrMoveIn[$key] = $arrIn;
                            endforeach;

                            $arrInv = (array) array_merge(
                                (array) $arrMoveIn
                            );

                            $arrData = (array) array_merge(
                                (array) $arrMoveOut,
                                (array) $arrMoveIn
                            );

                            $inventory->edit($arrInv);
                            $transaction->create($arrData);

                            $this->doMovementTerima($_ID, $_DocAction);
                        }
                    } else if (count($line) == 0 && $_DocAction === $this->DOCSTATUS_Completed) {
                        $this->entity->setDocStatus($this->DOCSTATUS_Invalid);
                        $response = $this->save();
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

    private function doMovementTerima($_ID, $_DocAction)
    {
        $changeLog = new M_ChangeLog($this->request);
        $cWfs = new WScenario();
        $menu = $this->request->uri->getSegment(2);

        $row = $this->model->find($_ID);
        $detail = $this->modelDetail->where($this->model->primaryKey, $_ID)->findAll();

        $docNo = $this->model->getInvNumber($this->Movement_Terima);

        //TODO: Insert movement IMT 
        $this->entity->setDocumentNo($docNo);
        $this->entity->setRefMovementId($row->getMovementId());
        $this->entity->setMovementDate($row->getMovementDate());
        $this->entity->setMovementType($this->Movement_Terima);
        $this->entity->setDocStatus($this->DOCSTATUS_Drafted);
        $this->entity->setBranchId($row->getBranchToId());
        $this->entity->setDivisionId($row->getDivisionId());
        $this->entity->setCreatedBy($this->access->getSessionUser());
        $this->entity->setUpdatedBy($this->access->getSessionUser());
        $this->model->save($this->entity);

        $insertID = $this->model->getInsertID();

        if ($insertID > 0) {
            //TODO: Insert Change Log IMT
            $changeLog->insertLog($this->model->table, $this->model->primaryKey, $insertID, null, $insertID, $this->EVENTCHANGELOG_Insert);

            //TODO: Insert batch movement detail 
            $dataInsert = $this->setField("trx_movement_id", $insertID, $detail);
            $dataInsert = $this->setField("ref_movement_detail_id", "ref_movement_detail_id", $detail, "trx_movement_detail_id");
            $dataInsert = $this->setField($this->createdByField, $this->access->getSessionUser(), $detail);
            $dataInsert = $this->setField($this->createdField, date("Y-m-d H:i:s"), $detail);
            $dataInsert = $this->setField($this->updatedByField, $this->access->getSessionUser(), $detail);
            $dataInsert = $this->setField($this->updatedField, date("Y-m-d H:i:s"), $detail);
            $this->modelDetail->insertBatch($dataInsert);

            //* Get list data movement detail 
            $newData = $this->modelDetail->where($this->model->primaryKey, $insertID)->findAll();

            //TODO: Insert Change Log movement detail
            foreach ($newData as $new) :
                $changeLog->insertLog($this->modelDetail->table, $this->modelDetail->primaryKey, $new->{$this->modelDetail->primaryKey}, null, $new->{$this->modelDetail->primaryKey}, $this->EVENTCHANGELOG_Insert);
            endforeach;

            //TODO: Set scenario 
            $cWfs->setScenario($this->entity, $this->model, $this->modelDetail, $insertID, $_DocAction, $menu, $this->session);

            //TODO: Update movement reference
            $this->entity->setDocumentNo($row->getDocumentNo());
            $this->entity->setRefMovementId($this->model->getInsertID());
            $this->entity->setMovementType($row->getMovementType());
            $this->entity->setDocStatus($row->getDocStatus());
            $this->entity->setMovementId($row->getMovementId());
            $this->entity->setWfScenarioId($row->getWfScenarioId());
            $this->save();

            //TODO: Update movement detail reference
            $dataUpdate = $this->setField("trx_movement_id", $_ID, $detail);
            $dataUpdate = $this->setField("ref_movement_detail_id", $newData, $detail, "trx_movement_detail_id");
            $dataUpdate = $this->setField($this->updatedByField, $this->access->getSessionUser(), $detail);
            $dataUpdate = $this->setField($this->updatedField, date("Y-m-d H:i:s"), $detail);
            $this->modelDetail->updateBatch($dataUpdate, $this->modelDetail->primaryKey);
        }

        return $insertID;
    }

    public function destroyLine($id)
    {
        if ($this->request->isAJAX()) {
            try {
                $result = $this->modelDetail->delete($id);
                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function destroyAllLine()
    {
        if ($this->request->isAJAX()) {
            $post = $this->request->getVar();

            try {
                $result = $this->modelDetail->where($this->model->primaryKey, $post['trx_movement_id'])->first();

                //? Exists data movement detail
                if ($result)
                    $result = $this->modelDetail->where($this->model->primaryKey, $post['trx_movement_id'])->delete();

                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function tableLine($set = null, $detail = [])
    {
        $post = $this->request->getVar();
        $uri = $this->request->uri->getSegment(2);

        $product = new M_Product($this->request);
        $employee = new M_Employee($this->request);
        $division = new M_Division($this->request);
        $branch = new M_Branch($this->request);
        $room = new M_Room($this->request);
        $inventory = new M_Inventory($this->request);
        $status = new M_Status($this->request);

        //! Get data role W_View_All_Movement  
        $role = $this->access->getUserRoleName($this->access->getSessionUser(), 'W_View_All_Movement');

        //* Data Product 
        $dataProduct = $product->where('isactive', 'Y')->findAll();

        //* Data Employee From 
        $dataEmployee = $employee->where('isactive', 'Y')->findAll();

        //* Data Division
        $dataDivision = $division->where('isactive', 'Y')->orderBy('name', 'ASC')->findAll();

        //* Data Branch
        $dataBranch = $branch->where('isactive', 'Y')->orderBy('name', 'ASC')->findAll();

        //* Data Room
        $dataRoom = $room->where('isactive', 'Y')->orderBy('name', 'ASC')->findAll();

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
            if (!$this->validation->run($post, 'movementAddRow')) {
                $table = $this->field->errorValidation($this->model->table, $post);
            } else {
                //? Where clause inventory 
                $invWhere["md_branch_id"] = $post["md_branch_id"];
                $invWhere['isactive'] = 'Y';

                //* Data Inventory 
                $dataInventory = $inventory->where($invWhere)->orderBy('assetcode', 'ASC')->findAll();

                //? Where Clause Employee to
                $empWhere['isactive'] = 'Y';
                $empWhere['md_branch_id'] = $post["md_branchto_id"];
                $empWhere['md_division_id'] = $post["md_division_id"];

                //* Data Employee To 
                $dataEmployeeTo = $employee->where($empWhere)->orderBy('name', 'ASC')->findAll();

                $table = [
                    $this->field->fieldTable('button', 'button', 'trx_movement_detail_id'),
                    $this->field->fieldTable('select', null, 'assetcode', 'unique', 'required', null, null, $dataInventory, null, 170, 'assetcode', 'assetcode'),
                    $this->field->fieldTable('select', null, 'md_product_id', null, null, 'readonly', null, $dataProduct, null, 300, 'md_product_id', 'name'),
                    $this->field->fieldTable('select', null, 'md_status_id', null, 'required', null, null, $dataStatus, 'BAGUS', 150, 'md_status_id', 'name'),
                    $this->field->fieldTable('select', null, 'employee_from', null, null, 'readonly', null, $dataEmployee, null, 200, 'md_employee_id', 'name'),
                    $this->field->fieldTable('select', null, 'employee_to', null, 'required', null, null, $dataEmployeeTo, null, 200, 'md_employee_id', 'name'),
                    $this->field->fieldTable('select', null, 'branch_from', null, null, 'readonly', null, $dataBranch, null, 200, 'md_branch_id', 'name'),
                    $this->field->fieldTable('select', null, 'branch_to', null, null, 'readonly', null, null, null, 200),
                    $this->field->fieldTable('select', null, 'division_from', null, null, 'readonly', null, $dataDivision, null, 200, 'md_division_id', 'name'),
                    $this->field->fieldTable('select', null, 'division_to', null, null, 'readonly', null, null, null, 200),
                    $this->field->fieldTable('select', null, 'room_from', null, null, 'readonly', null, $dataRoom, null, 250, 'md_room_id', 'name'),
                    $this->field->fieldTable('select', null, 'room_to', null, 'required', null, null, null, null, 250),
                    $this->field->fieldTable('input', 'text', 'description', null, null, null, null, null, null, 250)
                ];
            }
        }

        //? Update
        if (!empty($set) && count($detail) > 0) {
            $move = $this->model->find($detail[0]->trx_movement_id);

            foreach ($detail as $row) :
                //? Where Clause Employee To
                $empWhere['isactive'] = 'Y';
                $empWhere['md_branch_id'] = $row->branch_to;
                $empWhere['md_division_id'] = $row->division_to;

                //* Data Employee To 
                $dataEmployeeTo = $employee->where($empWhere)->orderBy('name', 'ASC')->findAll();

                $sessEmplo = $employee->where('sys_user_id', $this->session->get('sys_user_id'))->first();

                // if (($role || $sessEmplo && $row->employee_to == $sessEmplo->md_employee_id) && $move->getMovementType() === $this->Movement_Terima && $row->isaccept === "N") {
                //     $button = $this->field->fieldTable('button', 'button', 'trx_movement_detail_id', 'btn-success btn_accept', null, null, true, null, $row->trx_movement_detail_id);
                // } else {
                //     $button = $this->field->fieldTable('button', 'button', 'trx_movement_detail_id', null, null, null, null, null, $row->trx_movement_detail_id);
                // }

                // if ($move->getMovementType() === $this->Movement_Kirim)
                $button = $this->field->fieldTable('button', 'button', 'trx_movement_detail_id', null, null, null, null, null, $row->trx_movement_detail_id);

                //? Where clause inventory 
                // $invWhere["md_branch_id"] = $row->branch_from;

                $invWhere['isactive'] = 'Y';

                //* Data Inventory 
                $dataInventory = $inventory->where($invWhere)->orderBy('assetcode', 'ASC')->findAll();

                $table[] = [
                    $button,
                    $this->field->fieldTable('select', null, 'assetcode', 'unique', 'required', null, null, $dataInventory, $row->assetcode, 170, 'assetcode', 'assetcode'),
                    $this->field->fieldTable('select', null, 'md_product_id', null, null, 'readonly', null, $dataProduct, $row->md_product_id, 300, 'md_product_id', 'name'),
                    $this->field->fieldTable('select', null, 'md_status_id', null, 'required', null, null, $dataStatus, $row->md_status_id, 150, 'md_status_id', 'name'),
                    $this->field->fieldTable('select', null, 'employee_from', null, null, 'readonly', null, $dataEmployee, $row->employee_from, 200, 'md_employee_id', 'name'),
                    $this->field->fieldTable('select', null, 'employee_to', null, 'required', $row->status === 'RUSAK' ? 'readonly' : null, null, $dataEmployeeTo, $row->employee_to, 200, 'md_employee_id', 'name'),
                    $this->field->fieldTable('select', null, 'branch_from', null, null, 'readonly', null, $dataBranch, $row->branch_from, 200, 'md_branch_id', 'name'),
                    $this->field->fieldTable('select', null, 'branch_to', null, null, 'readonly', null, $dataBranch, $row->branch_to, 200, 'md_branch_id', 'name'),
                    $this->field->fieldTable('select', null, 'division_from', null, null, 'readonly', null, $dataDivision, $row->division_from, 200, 'md_division_id', 'name'),
                    $this->field->fieldTable('select', null, 'division_to', null, null, 'readonly', null, $dataDivision, $row->division_to, 200, 'md_division_id', 'name'),
                    $this->field->fieldTable('select', null, 'room_from', null, null, 'readonly', null, $dataRoom, $row->room_from, 250, 'md_room_id', 'name'),
                    $this->field->fieldTable('select', null, 'room_to', null, 'required', null, null, $dataRoom, $row->room_to, 250, 'md_room_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'description', null, null, null, null, null, $row->description, 250)
                ];
            endforeach;
        }

        return json_encode($table);
    }

    public function getList()
    {
        if ($this->request->isAjax()) {
            $post = $this->request->getVar();

            $response = [];

            try {
                if (isset($post['search'])) {
                    $list = $this->model->where('isactive', 'Y')
                        ->like('documentno', $post['search'])
                        ->orderBy('documentno', 'ASC')
                        ->findAll();
                } else {
                    $list = $this->model->where('isactive', 'Y')
                        ->orderBy('documentno', 'ASC')
                        ->findAll();
                }

                foreach ($list as $key => $row) :
                    $response[$key]['id'] = $row->getMovementId();
                    $response[$key]['text'] = $row->getDocumentNo();
                endforeach;
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function accept($id)
    {
        $mEmployee = new M_Employee($this->request);
        $mActivity = new M_WActivity($this->request);
        $mEvent = new M_WEvent($this->request);

        //! Get data role W_View_All_Movement  
        $role = $this->access->getUserRoleName($this->access->getSessionUser(), 'W_View_All_Movement');
        $sessEmplo = $mEmployee->where('sys_user_id', $this->session->get('sys_user_id'))->first();

        if (!$role)
            $where = [
                'employee_to'               => $sessEmplo->md_employee_id,
                $this->model->primaryKey    => $id
            ];
        else
            $where = [
                $this->model->primaryKey    => $id
            ];

        $detail = $this->modelDetail->where($where)->findAll();

        $dataUpdate = $this->setField("isaccept", "Y", $detail);
        $dataUpdate = $this->setField($this->updatedByField, $this->access->getSessionUser(), $detail);
        $dataUpdate = $this->setField($this->updatedField, date("Y-m-d H:i:s"), $detail);
        $result = $this->modelDetail->updateBatch($dataUpdate, $this->modelDetail->primaryKey);

        $line = $this->modelDetail->where([
            'isaccept'                  => "N",
            $this->model->primaryKey    => $id
        ])->first();

        if (!$line) {
            $this->entity->setDocStatus($this->DOCSTATUS_Completed);
            $this->entity->setMovementId($id);
            $this->save();

            $activity = $mActivity->where([
                "table"         => $this->model->table,
                "record_id"     => $id,
                "state"         => "OS",
                "processed"     => "N"
            ])->first();

            if ($activity) {
                $cActivity = new WActivity();
                $cActivity->entity->setSysUserId($this->session->get('sys_user_id'));
                $cActivity->entity->setState($this->DOCSTATUS_Completed);
                $cActivity->entity->setProcessed(true);
                $cActivity->entity->setWfActivityId($activity->sys_wfactivity_id);
                $cActivity->model->save($cActivity->entity);

                $event = $mEvent->where([
                    "table"         => $this->model->table,
                    "sys_wfactivity_id" => $activity->sys_wfactivity_id,
                    "record_id"     => $id,
                    "state"         => "OS",
                    "isapproved"     => "N"
                ])->findAll();

                $dataEvent = $this->setField("isapproved", "Y", $event);
                $dataEvent = $this->setField("state", $this->DOCSTATUS_Completed, $event);
                $dataEvent = $this->setField("sys_user_id", $this->session->get('sys_user_id'), $event);
                $dataEvent = $this->setField("oldvalue", "", $event);
                $dataEvent = $this->setField("newvalue", "true", $event);
                $dataEvent = $this->setField($this->updatedByField, $this->session->get('sys_user_id'), $event);
                $dataEvent = $this->setField($this->updatedField, date("Y-m-d H:i:s"), $event);
                $mEvent->updateBatch($dataEvent, $mEvent->primaryKey);

                $options = array(
                    'cluster' => 'ap1',
                    'useTLS' => true
                );
                $pusher = new Pusher(
                    '8ae4540d78a7d493226a',
                    '808c4eb78d03842672ca',
                    '1490113',
                    $options
                );

                $data['message'] = 'hello world';
                $pusher->trigger('my-channel', 'my-event', $data);
            }
        }

        $response = message('success', true, $result);

        return json_encode($response);
    }

    public function acceptLine($id)
    {
        $mEmployee = new M_Employee($this->request);
        $mActivity = new M_WActivity($this->request);
        $mEvent = new M_WEvent($this->request);

        //! Get data role W_View_All_Movement  
        $role = $this->access->getUserRoleName($this->access->getSessionUser(), 'W_View_All_Movement');
        $sessEmplo = $mEmployee->where('sys_user_id', $this->session->get('sys_user_id'))->first();

        if (!$role)
            $where = [
                'employee_to'               => $sessEmplo->md_employee_id,
                $this->modelDetail->primaryKey    => $id
            ];
        else
            $where = [
                $this->modelDetail->primaryKey    => $id
            ];

        $detail = $this->modelDetail->where($where)->findAll();

        $dataUpdate = $this->setField("isaccept", "Y", $detail);
        $dataUpdate = $this->setField($this->updatedByField, $this->access->getSessionUser(), $detail);
        $dataUpdate = $this->setField($this->updatedField, date("Y-m-d H:i:s"), $detail);
        $result = $this->modelDetail->updateBatch($dataUpdate, $this->modelDetail->primaryKey);

        $foreignKey = $detail[0]->trx_movement_id;

        $line = $this->modelDetail->where([
            'isaccept'                  => "N",
            $this->model->primaryKey    => $foreignKey
        ])->first();

        if (!$line) {
            $this->entity->setDocStatus($this->DOCSTATUS_Completed);
            $this->entity->setMovementId($foreignKey);
            $this->save();

            $activity = $mActivity->where([
                "table"         => $this->model->table,
                "record_id"     => $foreignKey,
                "state"         => "OS",
                "processed"     => "N"
            ])->first();

            if ($activity) {
                $cActivity = new WActivity();
                $cActivity->entity->setSysUserId($this->session->get('sys_user_id'));
                $cActivity->entity->setState($this->DOCSTATUS_Completed);
                $cActivity->entity->setProcessed(true);
                $cActivity->entity->setWfActivityId($activity->sys_wfactivity_id);
                $cActivity->model->save($cActivity->entity);

                $event = $mEvent->where([
                    "table"         => $this->model->table,
                    "sys_wfactivity_id" => $activity->sys_wfactivity_id,
                    "record_id"     => $foreignKey,
                    "state"         => "OS",
                    "isapproved"     => "N"
                ])->findAll();

                $dataEvent = $this->setField("isapproved", "Y", $event);
                $dataEvent = $this->setField("state", $this->DOCSTATUS_Completed, $event);
                $dataEvent = $this->setField("sys_user_id", $this->session->get('sys_user_id'), $event);
                $dataEvent = $this->setField("oldvalue", "", $event);
                $dataEvent = $this->setField("newvalue", "true", $event);
                $dataEvent = $this->setField($this->updatedByField, $this->session->get('sys_user_id'), $event);
                $dataEvent = $this->setField($this->updatedField, date("Y-m-d H:i:s"), $event);
                $mEvent->updateBatch($dataEvent, $mEvent->primaryKey);

                $options = array(
                    'cluster' => 'ap1',
                    'useTLS' => true
                );
                $pusher = new Pusher(
                    '8ae4540d78a7d493226a',
                    '808c4eb78d03842672ca',
                    '1490113',
                    $options
                );

                $data['message'] = 'hello world';
                $pusher->trigger('my-channel', 'my-event', $data);
            }
        }

        $response = message('success', true, $result);

        return json_encode($response);
    }
}
