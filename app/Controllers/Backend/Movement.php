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
use App\Models\M_ReferenceDetail;
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
        $mRef = new M_Reference($this->request);
        $mEmpl = new M_Employee($this->request);
        $mBranch = new M_Branch($this->request);
        $status = new M_Status($this->request);

        $uri = $this->request->uri->getSegment(2);
        $start_date = date('Y-m-d', strtotime('- 1 days'));
        $end_date = date('Y-m-d');

        $role = $this->access->getUserRoleName($this->access->getSessionUser(), 'W_View_All_Data');
        $employee = $mEmpl->where("sys_user_id", $this->access->getSessionUser())
            ->orderBy('name', 'ASC')
            ->first();

        $dataBranch = [];

        if ($employee && (!$role || ($role && $employee->getBranchId() != 100001))) {
            $branch = $mBranch->find($employee->getBranchId());
            $dataBranch['id'] = $branch->getBranchId();
            $dataBranch['text'] = $branch->getName();
        }

        $data = [
            'today'         => date('Y-m-d'),
            'ref_list'      => $mRef->findBy([
                'sys_reference.name'              => 'MovementType',
                'sys_reference.isactive'          => 'Y',
                'sys_ref_detail.isactive'         => 'Y',
                'sys_ref_detail.name'             => $this->Movement_Kirim
            ])->getRow(),
            'date_range'        => $start_date . ' - ' . $end_date,
            'branch'            => $dataBranch,
            'status'    => $status->where([
                'isactive'  => 'Y',
                'isline'    => 'N'
            ])->like('menu_id', $uri)
                ->orderBy('name', 'ASC')
                ->findAll(),
        ];

        return $this->template->render('transaction/movement/v_movement', $data);
    }

    public function showAll()
    {
        $mEmpl = new M_Employee($this->request);

        if ($this->request->getMethod(true) === 'POST') {
            $table = $this->model->table;
            $select = $this->model->getSelect();
            $join = $this->model->getJoin();
            $order = $this->model->column_order;
            $sort = $this->model->order;
            $search = $this->model->column_search;
            $where = [];

            $employee = $mEmpl->where("sys_user_id", $this->access->getSessionUser())->first();

            //? Check is user exist role W_View_All_Movement 
            if (!$this->access->getUserRoleName($this->access->getSessionUser(), 'W_View_All_Data')) {
                $arrMove = $this->model->getColumnArr($this->model->primaryKey);
                $arrLine = $this->modelDetail->getEmployeeToArr($this->model->primaryKey, $arrMove, $employee->getEmployeeId(), $this->model->primaryKey);

                if ($arrLine) {
                    $where['trx_movement.trx_movement_id'] = $arrLine;
                    $where['trx_movement.created_by'] = [
                        'condition' => 'OR',
                        'value'     => $this->access->getSessionUser()
                    ];
                } else {
                    $where['trx_movement.created_by'] = $this->access->getSessionUser();
                }
            }

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
                $row[] = $value->move_type;
                $row[] = $value->referenceno;
                $row[] = $value->branch;
                $row[] = $value->branchto;
                $row[] = $value->divisionto;
                $row[] = $value->status;
                $row[] = docStatus($value->docstatus, $value->movementtype, $totalLine, $avaiLine);
                $row[] = $value->createdby;
                $row[] = $value->description;
                $row[] = $this->template->tableButton($ID, $value->docstatus);
                $data[] = $row;
            endforeach;

            $result = [
                'draw'              => $this->request->getPost('draw'),
                'recordsTotal'      => $this->datatable->countAll($table, $select, $order, $sort, $search, $join, $where),
                'recordsFiltered'   => $this->datatable->countFiltered($table, $select, $order, $sort, $search, $join, $where),
                'data'              => $data
            ];

            return $this->response->setJSON($result);
        }
    }

    public function create()
    {
        $mEmpl = new M_Employee($this->request);

        if ($this->request->getMethod(true) === 'POST') {
            $post = $this->request->getVar();

            $table = json_decode($post['table']);

            //! Mandatory property for detail validation
            $post['line'] = countLine($table);
            $post['detail'] = [
                'table' => arrTableLine($table)
            ];

            try {
                if (!$this->validation->run($post, 'movement')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    $employee = $mEmpl->where("sys_user_id", $this->access->getSessionUser())->first();

                    $division_id = $employee ? $employee->getDivisionId() : 100022;

                    if (empty($post['ref_movement_id']))
                        unset($post['ref_movement_id']);

                    if (empty($post['movementstatus']))
                        unset($post['movementstatus']);

                    $this->entity->fill($post);
                    $this->entity->setDivisionId($division_id);

                    //* Insert data
                    if ($this->isNew()) {
                        $this->entity->setDocStatus($this->DOCSTATUS_Drafted);

                        $docNo = $this->model->getInvNumber($post['movementtype'], $post['movementdate']);
                        $this->entity->setDocumentNo($docNo);
                    }

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
        $branch = new M_Branch($this->request);
        $division = new M_Division($this->request);
        $refDetail = new M_ReferenceDetail($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();
                $detail = $this->modelDetail->where($this->model->primaryKey, $id)->findAll();

                if (!empty($list[0]->getRefMovementId())) {
                    $rowRefMove = $this->model->find($list[0]->getRefMovementId());
                    $list = $this->field->setDataSelect($this->model->table, $list, "ref_movement_id", $rowRefMove->getMovementId(), $rowRefMove->getDocumentNo());
                }

                $rowMoveType = $refDetail->where("name", $list[0]->getMovementType())->first();
                $rowBranch = $branch->find($list[0]->getBranchId());

                $list = $this->field->setDataSelect($refDetail->table, $list, "movementtype", $rowMoveType->getValue(), $rowMoveType->getName());
                $list = $this->field->setDataSelect($branch->table, $list, $branch->primaryKey, $rowBranch->getBranchId(), $rowBranch->getName());

                if (!empty($list[0]->getBranchToId())) {
                    $rowDivision = $division->find($list[0]->getDivisionToId());
                    $list = $this->field->setDataSelect($division->table, $list, "md_divisionto_id", $rowDivision->getDivisionId(), $rowDivision->getName());
                }

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
            $post = $this->request->getVar();

            $_ID = $post['id'];
            $_DocAction = $post['docaction'];

            try {
                $row = $this->model->find($_ID);
                $line = $this->modelDetail->where($this->model->primaryKey, $_ID)->findAll();
                $imt = $this->model->where([
                    'movementtype'      => $this->Movement_Terima,
                    'ref_movement_id'   => $_ID,
                    'docstatus <>'      => $this->DOCSTATUS_Voided
                ])->first();

                if (!empty($_DocAction)) {
                    if ($_DocAction === $row->getDocStatus()) {
                        $response = message('error', true, 'Please reload the Document');
                    } else if ($_DocAction === $this->DOCSTATUS_Completed) {
                        if ($line) {
                            $this->entity->setDocStatus($this->DOCSTATUS_Completed);

                            //* Set movement status Internal Move 
                            if ($row->getBranchId() != 100001 && $row->getBranchId() == $row->getBranchToId()) {
                                $this->entity->setMovementStatus(100008);
                            }

                            //* Set movement status Internal Move
                            if ($row->getBranchId() == 100001 && $row->getBranchId() == $row->getBranchToId() && $row->getDivisionId() == $row->getDivisionToId()) {
                                $this->entity->setMovementStatus(100008);
                            }

                            $response = $this->save();

                            $row = $this->model->find($_ID);

                            if (empty($row->getMovementStatus())) {
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
                                    $arrIn->md_branch_id = $value->branch_to;
                                    $arrIn->md_division_id = $value->division_to;
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
                            } else {
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

                                    $arrIn->assetcode = $value->assetcode;
                                    $arrIn->md_product_id = $value->md_product_id;
                                    $arrIn->md_employee_id = $value->employee_to;
                                    $arrIn->md_branch_id = $value->branch_to;
                                    $arrIn->md_division_id = $value->division_to;
                                    $arrIn->md_room_id = $value->room_to;
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
                            }
                        } else {
                            $this->entity->setDocStatus($this->DOCSTATUS_Invalid);
                            $response = $this->save();
                        }
                    } else if ($_DocAction === $this->DOCSTATUS_Unlock && !$imt && $row->getMovementType() === $this->Movement_Kirim) {
                        $this->entity->setDocStatus($this->DOCSTATUS_Drafted);
                        $response = $this->save();
                    } else if ($imt && ($_DocAction === $this->DOCSTATUS_Unlock || $_DocAction === $this->DOCSTATUS_Voided) && $row->getMovementType() === $this->Movement_Kirim) {
                        $response = message('error', true, 'Please Void Movement Terima first');
                    } else if ($_DocAction === $this->DOCSTATUS_Unlock && $row->getMovementType() === $this->Movement_Terima) {
                        $response = message('error', true, 'Cannot be processed, please void this movement');
                    } else {
                        $this->entity->setDocStatus($_DocAction);
                        $response = $this->save();

                        if ($_DocAction === $this->DOCSTATUS_Voided && ($row->getMovementType() === $this->Movement_Kirim && !empty($row->getMovementStatus()) || $row->getMovementType() === $this->Movement_Terima)) {
                            //* Passing data to table transaction
                            $arrMoveIn = [];
                            $arrMoveOut = [];
                            foreach ($line as $key => $value) :
                                //? Data movement to
                                $arrOut = new stdClass();

                                if ($row->getDocStatus === $this->DOCSTATUS_NotApproved && $row->getMovementType() === $this->Movement_Terima) {
                                    $room = new M_Room($this->request);
                                    $transit = $room->where("name", "TRANSIT")->first();
                                    $arrOut->md_room_id = $transit->md_room_id;
                                }

                                if ($row->getMovementType() === $this->Movement_Kirim || $row->getMovementType() === $this->Movement_Terima && $row->getDocStatus() === $this->DOCSTATUS_Completed) {
                                    $arrOut->md_room_id = $value->room_to;
                                }

                                $arrOut->assetcode = $value->assetcode;
                                $arrOut->md_product_id = $value->md_product_id;
                                $arrOut->md_employee_id = $value->employee_to;
                                $arrOut->transactiontype = $this->Movement_Out;
                                $arrOut->transactiondate = date("Y-m-d");
                                $arrOut->qtyentered = -1;
                                $arrOut->trx_movement_detail_id = $value->trx_movement_detail_id;
                                $arrMoveOut[$key] = $arrOut;

                                //? Data movement from
                                $arrIn = new stdClass();
                                $arrIn->assetcode = $value->assetcode;
                                $arrIn->md_product_id = $value->md_product_id;
                                $arrIn->md_employee_id = $value->employee_from;
                                $arrIn->md_branch_id = $value->branch_from;
                                $arrIn->md_division_id = $value->division_from;
                                $arrIn->md_room_id = $value->room_from;
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
                        }
                    }
                } else {
                    $response = message('error', true, 'Please Choose the Document Action first');
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

        $movementDate = $row->getMovementDate();
        $docNo = $this->model->getInvNumber($this->Movement_Terima, $movementDate);

        //TODO: Insert movement IMT 
        $this->entity->setDocumentNo($docNo);
        $this->entity->setRefMovementId($row->getMovementId());
        $this->entity->setMovementDate($movementDate);
        $this->entity->setMovementType($this->Movement_Terima);
        $this->entity->setDocStatus($this->DOCSTATUS_Drafted);
        $this->entity->setBranchId($row->getBranchToId());
        $this->entity->setDivisionId($row->getDivisionToId());
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
            $this->entity->setBranchId($row->getBranchId());
            $this->entity->setDocStatus($row->getDocStatus());
            $this->entity->setMovementId($row->getMovementId());
            $this->entity->setWfScenarioId($row->getWfScenarioId());
            $this->entity->setCreatedBy($row->getCreatedBy());
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
        $product = new M_Product($this->request);
        $employee = new M_Employee($this->request);
        $division = new M_Division($this->request);
        $branch = new M_Branch($this->request);
        $room = new M_Room($this->request);
        $inventory = new M_Inventory($this->request);
        $status = new M_Status($this->request);

        $post = $this->request->getVar();
        $uri = $this->request->uri->getSegment(2);

        //! Get data role W_View_All_Data
        $role = $this->access->getUserRoleName($this->access->getSessionUser(), 'W_View_All_Data');

        //! Get data Employee based on sys_user_id login 
        $dataEmpl = $employee->where("sys_user_id", $this->access->getSessionUser())
            ->orderBy('name', 'ASC')
            ->first();

        //* Data Division
        $dataDivision = $division->where('isactive', 'Y')->orderBy('name', 'ASC')->findAll();

        //* Data Branch
        $dataBranch = $branch->where('isactive', 'Y')->orderBy('name', 'ASC')->findAll();

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

                //? Doesn't have Role W_View_All_Data
                if ($dataEmpl && !$role)
                    $invWhere["md_division_id"] = $dataEmpl->getDivisionId();

                //* Data Inventory 
                $dataInventory = $inventory->where($invWhere)->orderBy('assetcode', 'ASC')->findAll();

                //? Where Clause Employee to
                $empWhere['isactive'] = 'Y';
                $empWhere['md_branch_id'] = $post["md_branchto_id"];

                //? Spesific Division from Branch To Sunter or Not ALL DIVISION
                if ($post['md_branchto_id'] == 100001 || ($post['md_branchto_id'] != 100001 && $post["md_divisionto_id"] != 100022))
                    $empWhere['md_division_id'] = $post["md_divisionto_id"];

                //* Data Employee To 
                $dataEmployeeTo = $employee->where($empWhere)->orderBy('name', 'ASC')->findAll();

                $table = [
                    $this->field->fieldTable('button', 'button', 'trx_movement_detail_id'),
                    $this->field->fieldTable('select', null, 'assetcode', 'unique', 'required', null, null, $dataInventory, null, 170, 'assetcode', 'assetcode'),
                    $this->field->fieldTable('input', 'text', 'md_product_id', null, 'required', 'readonly', null, null, null, 300),
                    $this->field->fieldTable('select', null, 'md_status_id', null, 'required', null, null, $dataStatus, 'BAGUS', 150, 'md_status_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'employee_from', null, 'required', 'readonly', null, null, null, 200),
                    $this->field->fieldTable('select', null, 'employee_to', null, 'required', null, null, $dataEmployeeTo, null, 200, 'md_employee_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'branch_from', null, 'required', 'readonly', null, null, null, 200),
                    $this->field->fieldTable('select', null, 'branch_to', null, null, 'readonly', null, null, null, 200),
                    $this->field->fieldTable('input', 'text', 'division_from', null, 'required', 'readonly', null, null, null, 200),
                    $this->field->fieldTable('select', null, 'division_to', null, null, 'readonly', null, null, null, 200),
                    $this->field->fieldTable('input', 'text', 'room_from', null, 'required', 'readonly', null, null, null, 200),
                    $this->field->fieldTable('select', null, 'room_to', null, 'required', null, null, null, null, 250),
                    $this->field->fieldTable('input', 'text', 'description', null, null, null, null, null, null, 250)
                ];
            }
        }

        //? Update
        if (!empty($set) && count($detail) > 0) {
            $move = $this->model->find($detail[0]->trx_movement_id);

            foreach ($detail as $row) :
                $valPro = $product->find($row->md_product_id);
                $valBranch = $branch->find($row->branch_from);
                $valEmp = $employee->find($row->employee_from);
                $valDiv = $division->find($row->division_from);
                $valRoom = $room->find($row->room_from);

                //? Where Clause Employee To
                $empWhere['isactive'] = 'Y';
                $empWhere['md_branch_id'] = $row->branch_to;

                //? Spesific Division from Branch To Sunter
                if ($move->getBranchToId() == 100001 || ($move->getBranchToId() != 100001 && $move->getDivisionToId() != 100022))
                    $empWhere['md_division_id'] = $row->division_to;

                //* Data Employee To 
                $dataEmployeeTo = $employee->where($empWhere)->orderBy('name', 'ASC')->findAll();

                // $sessEmplo = $employee->where('sys_user_id', $this->session->get('sys_user_id'))->first();
                // if (($role || $sessEmplo && $row->employee_to == $sessEmplo->md_employee_id) && $move->getMovementType() === $this->Movement_Terima && $row->isaccept === "N") {
                //     $button = $this->field->fieldTable('button', 'button', 'trx_movement_detail_id', 'btn-success btn_accept', null, null, true, null, $row->trx_movement_detail_id);
                // } else {
                //     $button = $this->field->fieldTable('button', 'button', 'trx_movement_detail_id', null, null, null, null, null, $row->trx_movement_detail_id);
                // }

                //? Where clause inventory 
                $invWhere['md_branch_id'] = $move->getBranchId();
                $invWhere['isactive'] = 'Y';
                $invOrWhere = [];

                //? Doesn't have Role W_View_All_Data
                if ($dataEmpl && !$role) {
                    $invWhere["md_division_id"] = $move->getDivisionId();
                    $invOrWhere["assetcode"] = $row->assetcode;
                }

                //? Where clause room to 
                $roomWhere['isactive'] = 'Y';
                if ($move->getMovementType() === $this->Movement_Terima)
                    $roomWhere['md_branch_id'] = $move->getBranchId();
                else
                    $roomWhere['md_branch_id'] = $move->getBranchToId();

                //* Data Inventory 
                if ($move->getDocStatus() !== $this->DOCSTATUS_Completed)
                    $dataInventory = $inventory->where($invWhere)->orWhere($invOrWhere)->orderBy('assetcode', 'ASC')->findAll();
                else
                    $dataInventory = $inventory->orderBy('assetcode', 'ASC')->findAll();

                //* Data Room To
                $dataRoomTo = $room->where($roomWhere)->orderBy('name', 'ASC')->findAll();

                $table[] = [
                    $this->field->fieldTable('button', 'button', 'trx_movement_detail_id', null, null, null, null, null, $row->trx_movement_detail_id),
                    $this->field->fieldTable('select', null, 'assetcode', 'unique', 'required', null, null, $dataInventory, $row->assetcode, 170, 'assetcode', 'assetcode'),
                    $this->field->fieldTable('input', 'text', 'md_product_id', null, 'required', 'readonly', null, null, $valPro->getName(), 300),
                    $this->field->fieldTable('select', null, 'md_status_id', null, 'required', null, null, $dataStatus, $row->md_status_id, 150, 'md_status_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'employee_from', null, 'required', 'readonly', null, null, $valEmp->getName(), 200),
                    $this->field->fieldTable('select', null, 'employee_to', null, 'required', $row->status === 'RUSAK' ? 'readonly' : null, null, $dataEmployeeTo, $row->employee_to, 200, 'md_employee_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'branch_from', null, 'required', 'readonly', null, null, $valBranch->getName(), 200),
                    $this->field->fieldTable('select', null, 'branch_to', null, null, 'readonly', null, $dataBranch, $row->branch_to, 200, 'md_branch_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'division_from', null, 'required', 'readonly', null, null, $valDiv->getName(), 200),
                    $this->field->fieldTable('select', null, 'division_to', null, null, 'readonly', null, $dataDivision, $row->division_to, 200, 'md_division_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'room_from', null, 'required', 'readonly', null, null, $valRoom->getName(), 200),
                    $this->field->fieldTable('select', null, 'room_to', 'updatable', 'required', null, null, $dataRoomTo, $row->room_to, 250, 'md_room_id', 'name'),
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
