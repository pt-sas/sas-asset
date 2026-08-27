<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_AlertRecipient;
use App\Models\M_Depreciation;
use App\Models\M_DepreciationDetail;
use App\Models\M_DisposalDetail;
use App\Models\M_Employee;
use App\Models\M_MovementDetail;
use App\Models\M_Responsible;
use App\Models\M_User;
use App\Models\M_WActivity;
use App\Models\M_WEvent;
use App\Models\M_WScenarioDetail;
use App\Models\M_Transaction;
use App\Models\M_Inventory;
use App\Models\M_Menu;
use App\Models\M_Movement;
use App\Models\M_Room;
use App\Models\M_ServiceDetail;
use Config\Services;
use Pusher\Pusher;
use Html2Text\Html2Text;
use stdClass;

class WActivity extends BaseController
{
    protected $wfScenarioId = 0;
    protected $wfResponsibleId = [];

    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_WActivity($this->request);
        $this->entity = new \App\Entities\WActivity();
    }

    private function toForwardAlert($table, $record_id, $subject, $message)
    {
        $mAlert = new M_AlertRecipient($this->request);
        $cMail = new Mail();
        $alert = $mAlert->getAlertRecipient($table, $record_id);

        $result = false;

        if ($alert) {
            foreach ($alert as $val) :
                if (!empty($val->email))
                    $result = $cMail->sendEmail($val->email, $subject, $message, null, "SAS Asset");
            endforeach;
        }

        return $result;
    }

    public function showActivityInfo()
    {
        $mMenu = new M_Menu($this->request);

        if ($this->request->isAjax()) {
            $data = [];
            $list = $this->model->getActivity();

            $result = [];

            if ($list) {
                foreach ($list as $value) :
                    $row = [];
                    $ID = $value->sys_wfactivity_id;
                    $record_id = $value->record_id;
                    $table = $value->table;
                    $menu = $value->menu;
                    $tableLine = $value->tableline;
                    $recordLine_id = $value->recordline_id;

                    $menuName = $mMenu->getMenuBy($menu);
                    $node = 'Approval ' . ucwords($menuName);

                    if ($tableLine) {
                        $trx = $this->model->getDataTrx($table, $recordLine_id, $tableLine);
                    } else {
                        $trx = $this->model->getDataTrx($table, $record_id);
                    }

                    if ($trx && $table == 'trx_service') {
                        $summary = ucwords($menuName) . ' ' . $trx->documentno . ' : ' . $trx->usercreated_by . ' : ' . $trx->assetcode . " : Amount " . formatRupiah($trx->lineamt);
                    } else if ($trx) {
                        $summary = ucwords($menuName) . ' ' . $trx->documentno . ': ' . $trx->usercreated_by;
                    } else {
                        $summary = ucwords($menuName) . ' ' . $record_id;
                    }

                    $row[] = $ID;
                    $row[] = $record_id;
                    $row[] = $table;
                    $row[] = $menu;
                    $row[] = $node;
                    $row[] = $summary;
                    $data[] = $row;
                endforeach;
            }

            $result = [
                'data'              => $data
            ];

            return $this->response->setJSON($result);
        }
    }

    public function setActivity($sys_wfactivity_id, $sys_wfscenario_id, $sys_wfresponsible_id, $user_by, $state, $processed, $textmsg, $table, $record_id, $menu, $tableLine = null, $recordLine_id = null)
    {
        $mWr = new M_Responsible($this->request);
        $mWe = new M_WEvent($this->request);
        $mUser = new M_User($this->request);
        $cMail = new Mail();
        $mMenu = new M_Menu($this->request);

        $this->entity->setWfScenarioId($sys_wfscenario_id);
        $this->entity->setTable($table);
        $this->entity->setRecordId($record_id);
        $this->entity->setMenu($menu);
        $this->entity->setTableLine($tableLine);
        $this->entity->setRecordLineId($recordLine_id);

        $user_id = $mWr->getUserByResponsible($sys_wfresponsible_id);
        $menuName = $mMenu->getMenuBy($menu);

        if (empty($sys_wfactivity_id)) {
            $this->entity->setWfResponsibleId($sys_wfresponsible_id);
            $this->entity->setSysUserId($user_id);
            $this->entity->setState($state);
            $this->entity->setTextMsg($textmsg);
            $this->entity->setProcessed($processed);
            $this->entity->setCreatedBy($user_by);
            $this->entity->setUpdatedBy($user_by);
            $result = $this->model->save($this->entity);

            $sys_wfactivity_id = $this->model->getInsertID();
            $mWe->setEventAudit($sys_wfactivity_id, $sys_wfresponsible_id, $user_id, $state, $processed, $table, $record_id, $user_by, false, $tableLine, $recordLine_id);

            $resp = $mWr->find($sys_wfresponsible_id);
            $list = $mUser->detail(['sr.sys_role_id' => $resp->getRoleId()])->getResult();

            $builder = $this->getBuilder($table);
            $builder->where($this->getPrimaryKey($table), $record_id);
            $sql = $builder->get()->getRow();
            $subject = ucwords($menuName) . "_" . $sql->documentno;
            $message =  '<p>Dear Mr/Ms,</p><p><span style="letter-spacing: 0.05em;">Please approve document below.</span></p><div><br></div>';
            $message .= "-----" . " " . ucwords($menuName) . " ";

            if ($tableLine == 'trx_service_detail' && $recordLine_id) {
                $builderLine = $this->getBuilder($tableLine);
                $builderLine->where($this->getPrimaryKey($tableLine), $recordLine_id);
                $sqlLine = $builderLine->get()->getRow();

                $message .= $sql->documentno . ": Assetcode = " . $sqlLine->assetcode . " : Amount = " . formatRupiah($sqlLine->lineamt);
            } else  if (isset($sql->grandtotal)) {
                $message .= $sql->documentno . ": Approval Amount =" . formatRupiah($sql->grandtotal);
            } else {
                $message .= $sql->documentno;
            }

            $message = new Html2Text($message);
            $message = $message->getText();

            foreach ($list as $key => $user) :
                $cMail->sendEmail($user->email, $subject, $message, null, "SAS Asset");
            endforeach;

            $this->toForwardAlert('sys_wfresponsible', $sys_wfresponsible_id, $subject, $message);

            if (isset($sql->movementtype) && $sql->movementtype === $this->Movement_Terima) {
                $mMoveDetail = new M_MovementDetail($this->request);
                $mEmployee = new M_Employee($this->request);

                $detail = $mMoveDetail->where([
                    "trx_movement_id"   => $sql->trx_movement_id
                ])->findAll();

                $listEmployee = [];
                foreach ($detail as $val) :
                    $listEmployee[] = $val->employee_to;
                endforeach;

                $listEmployee = array_unique($listEmployee);

                $list = $mEmployee->whereIn("md_employee_id", $listEmployee)->findAll();

                $message =  '<p>Dear Mr/Ms,</p><p><span style="letter-spacing: 0.05em;">Please received asset.</span></p><div><br></div>';
                $message .= "-----" . " " . ucwords($menuName) . " ";

                $message = new Html2Text($message);
                $message = $message->getText();

                foreach ($list as $row) :
                    if (!empty($row->sys_user_id)) {
                        $user = $mUser->find($row->sys_user_id);

                        if (!empty($user->email))
                            $cMail->sendEmail($user->email, $subject, $message, null, "SAS Asset");
                    }
                endforeach;
            }

            if ($tableLine && $recordLine_id) {
                $mServiceDetail = new M_ServiceDetail($this->request);

                $mServiceDetail->where($mServiceDetail->primaryKey, $recordLine_id)->set('isagree', 'H')->update();
            }
        } else {
            if (!empty($this->getNextResponsible())) {
                $newWfResponsibleId = $this->getNextResponsible();
                $user_id = $mWr->getUserByResponsible($newWfResponsibleId);

                $mWe->setEventAudit($sys_wfactivity_id, $sys_wfresponsible_id, $user_id, $state, $processed, $table, $record_id, $user_by, true, $tableLine, $recordLine_id);

                $sys_wfresponsible_id = $newWfResponsibleId;
                $user = $mUser->find($user_by);
                $resp = $mWr->find($sys_wfresponsible_id);
                $msg = 'Approved By : ' . $user->getUserName() . ' </br> ';

                $msg .= 'Next Approver : ' . $resp->getName() . ' </br> ';

                $msg .= $textmsg;
                $this->entity->setTextMsg($msg);

                if ($state === $this->DOCSTATUS_Completed && $processed) {
                    $state = $this->DOCSTATUS_Suspended;
                    $processed = false;
                    $mWe->setEventAudit($sys_wfactivity_id, $sys_wfresponsible_id, $user_id, $state, $processed, $table, $record_id, $user_by, false, $tableLine, $recordLine_id);
                }

                $resp = $mWr->find($sys_wfresponsible_id);
                $list = $mUser->detail(['sr.sys_role_id' => $resp->getRoleId()])->getResult();

                $builder = $this->getBuilder($table);
                $builder->where($this->getPrimaryKey($table), $record_id);
                $sql = $builder->get()->getRow();
                $subject = ucwords($menuName) . "_" . $sql->documentno;
                $message =  '<p>Dear Mr/Ms,</p><p><span style="letter-spacing: 0.05em;">Please approve document below.</span></p><div><br></div>';
                $message .= "-----" . " " . ucwords($menuName) . " ";

                if ($tableLine == 'trx_service_detail' && $recordLine_id) {
                    $builderLine = $this->getBuilder($tableLine);
                    $builderLine->where($this->getPrimaryKey($tableLine), $recordLine_id);
                    $sqlLine = $builderLine->get()->getRow();

                    $message .= $sql->documentno . ": Assetcode = " . $sqlLine->assetcode . " : Amount = " . formatRupiah($sqlLine->lineamt);
                } else if (isset($sql->grandtotal)) {
                    $message .= $sql->documentno . ": Approval Amount =" . formatRupiah($sql->grandtotal);
                } else {
                    $message .= $sql->documentno;
                }

                $message = new Html2Text($message);
                $message = $message->getText();

                foreach ($list as $key => $user) :
                    $cMail->sendEmail($user->email, $subject, $message, null, "SAS Asset");
                endforeach;

                $this->toForwardAlert('sys_wfresponsible', $sys_wfresponsible_id, $subject, $message);
            } else {
                $trxTable = $tableLine ? $tableLine : $table;
                $trxID = $tableLine ? $recordLine_id : $record_id;

                $builder = $this->model->db->table($trxTable);

                if ($state === $this->DOCSTATUS_Aborted && $processed) {
                    $mWe->setEventAudit($sys_wfactivity_id, $sys_wfresponsible_id, $user_id, $state, $processed, $table, $record_id, $user_by, false, $tableLine, $recordLine_id);

                    $data = [];

                    if ($trxTable == $tableLine) {
                        $data['isagree'] = 'N';
                    } else {
                        $data['docstatus'] = $this->DOCSTATUS_NotApproved;
                    }

                    $builder->where($this->getPrimaryKey($trxTable), $trxID)->update($data);
                } else {
                    $state = $this->DOCSTATUS_Completed;
                    $processed = true;
                    $mWe->setEventAudit($sys_wfactivity_id, $sys_wfresponsible_id, $user_id, $state, $processed, $table, $record_id, $user_by, false, $tableLine, $recordLine_id);

                    if ($trxTable == $tableLine) {
                        $data['isagree'] = 'Y';
                    } else {
                        $data['docstatus'] = $state;
                    }

                    $builder->where($this->getPrimaryKey($trxTable), $trxID)->update($data);

                    $builder = $this->getBuilder($table);
                    $builder->where($this->getPrimaryKey($table), $record_id);
                    $sql = $builder->get()->getRow();
                    $subject = ucwords($menuName) . "_" . $sql->documentno;
                    $message =  'Sudah Di Approve' . "<br>";
                    $message .= "---" . "<br>";
                    $message .= ucwords($menuName) . " " . $sql->documentno . "<br>";

                    if (isset($sql->grandtotal))
                        $message .= "Approval Amount = " . formatRupiah($sql->grandtotal) . "<br>";

                    $message .= $sql->description;
                    $message = new Html2Text($message);
                    $message = $message->getText();

                    $user = $mUser->find($sql->created_by);
                    $cMail->sendEmail($user->email, $subject, $message, null, "SAS Asset");

                    $this->toForwardAlert('sys_wfresponsible', $sys_wfresponsible_id, $subject, $message);
                }
            }

            $this->entity->setWfResponsibleId($sys_wfresponsible_id);
            $this->entity->setSysUserId($user_id);
            $this->entity->setState($state);
            $this->entity->setProcessed($processed);
            $this->entity->setUpdatedBy($user_by);
            $this->entity->setWfActivityId($sys_wfactivity_id);
            $result = $this->model->save($this->entity);

            if ($this->entity->getState() === $this->DOCSTATUS_Completed && isset($sql->movementtype)) {
                $inventory = new M_Inventory($this->request);
                $transaction = new M_Transaction();
                $mMoveDetail = new M_MovementDetail($this->request);
                $cMove = new Movement();

                if ($sql->movementtype === $this->Movement_Terima) {
                    $mEmployee = new M_Employee($this->request);

                    $detail = $mMoveDetail->where([
                        "isaccept"          => "N",
                        "trx_movement_id"   => $sql->trx_movement_id
                    ])->findAll();

                    $dataUpdate = $this->setField("isaccept", "Y", $detail);
                    $dataUpdate = $this->setField($this->updatedByField, $user_id, $detail);
                    $dataUpdate = $this->setField($this->updatedField, date("Y-m-d H:i:s"), $detail);
                    $mMoveDetail->updateBatch($dataUpdate, $mMoveDetail->primaryKey);

                    $line = $mMoveDetail->where([
                        "trx_movement_id"   => $sql->trx_movement_id
                    ])->findAll();

                    $listEmployee = [];
                    foreach ($line as $val) :
                        $listEmployee[] = $val->employee_from;
                    endforeach;

                    $listEmployee = array_unique($listEmployee);

                    $list = $mEmployee->whereIn("md_employee_id", $listEmployee)->findAll();

                    $message =  '<p>Dear Mr/Ms,</p><p><span style="letter-spacing: 0.05em;">Sudah Di Terima.</span></p><div><br></div>';
                    $message .= "-----" . " " . ucwords($menuName) . " ";

                    $message = new Html2Text($message);
                    $message = $message->getText();

                    foreach ($list as $row) :
                        if (!empty($row->sys_user_id)) {
                            $user = $mUser->find($row->sys_user_id);

                            if (!empty($user->email))
                                $cMail->sendEmail($user->email, $subject, $message, null, "SAS Asset");
                        }
                    endforeach;

                    foreach ($line as $key => $value) :
                        //? Data movement from
                        $arrOut = new stdClass();
                        $mRoom = new M_Room($this->request);
                        $transit = $mRoom->where("name", "TRANSIT")->first();

                        $arrOut->assetcode = $value->assetcode;
                        $arrOut->md_product_id = $value->md_product_id;
                        $arrOut->md_employee_id = $value->employee_to;
                        $arrOut->md_room_id = $transit->md_room_id;
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
                        $arrIn->isspare = "N";
                        $arrIn->isnew = "N";
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

                if ($sql->movementtype === $this->Movement_Kirim) {
                    $line = $mMoveDetail->where([
                        "trx_movement_id"   => $sql->trx_movement_id
                    ])->findAll();

                    //? Status not DIFFERENT DIVISION
                    if ($sql->movementstatus == 100009) {
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
                            $arrIn->isnew = "N";
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

                        $cMove->doMovementTerima($record_id, $this->DOCSTATUS_Completed, $user_by);
                    }
                }
            }

            if ($this->entity->getState() === $this->DOCSTATUS_Completed && $table == 'trx_disposal') {
                $inventory = new M_Inventory($this->request);
                $disposalDetail = new M_DisposalDetail($this->request);
                $depreciation = new M_Depreciation($this->request);
                $depreciationDetail = new M_DepreciationDetail($this->request);

                $line = $disposalDetail->where('trx_disposal_id', $record_id)->findAll();

                // TODO : Update Next Period Depreciation Detail
                $dateDispose = date('d', strtotime($sql->disposaldate));
                $dateDisposal = date('Y-m-d', strtotime($sql->disposaldate));
                $isLastDateDisposal =  $dateDisposal == date('Y-m-t', strtotime($sql->disposaldate));

                $assetList = array_map(fn($row) => $row->assetcode, $line);
                $allInventory = $inventory->whereIn('assetcode', $assetList)->findAll();

                //* Fetch ALL depreciation detail rows for these assets once, up front.
                $depreciationDetailByAsset = [];
                foreach ($depreciationDetail->whereIn('assetcode', $assetList)->findAll() as $row) {
                    $depreciationDetailByAsset[$row->assetcode][] = $row;
                }

                //* This for update Inventory Table later
                $dataInventory = [];

                //* Data pool Data Depreciation Detail updates
                $dataDepreDetail = [];

                //* Tracks each asset's OWN disposal period.
                $periodDisposalMap = [];

                foreach ($allInventory as $asset) {
                    //* Settle Condition Var
                    $inventoryDay = date('d', strtotime($asset->inventorydate));
                    $isDisposeBigThanAcq = $inventoryDay <= $dateDispose;

                    $isLastDate = $isLastDateDisposal ? date('Y-m-d', strtotime($asset->inventorydate)) == date('Y-m-t', strtotime($asset->inventorydate)) : false;

                    $periodDisposal = ($isLastDate || $isDisposeBigThanAcq) ? date('Y-m', strtotime($sql->disposaldate)) : date('Y-m', strtotime('-1 month', strtotime($sql->disposaldate)));

                    $periodDisposalMap[$asset->assetcode] = $periodDisposal;

                    foreach ($depreciationDetailByAsset[$asset->assetcode] ?? [] as $row) {
                        if ($row->period > $periodDisposal) {
                            $dataDepreDetail[] = [
                                'trx_depreciation_detail_id' => $row->trx_depreciation_detail_id,
                                'bookvalue' => 0,
                                'accumulateddepreciation' => 0,
                                'costdepreciation' => 0
                            ];
                        }
                    }

                    $dataInventory[] = ['trx_inventory_id' => $asset->trx_inventory_id, 'isdisposed' => 'Y'];
                }

                if (!empty($dataDepreDetail))
                    $depreciationDetail->updateBatch($dataDepreDetail, 'trx_depreciation_detail_id');

                // TODO : Update Current & Next Year Depreciation
                $yearDisposal = date('Y', strtotime($sql->disposaldate));

                $allDisposedDepreciation = [];
                foreach ($depreciationDetailByAsset as $assetcode => $rows) {
                    if (!isset($periodDisposalMap[$assetcode])) {
                        continue;
                    }

                    foreach ($rows as $row) {
                        if ($row->period == $periodDisposalMap[$assetcode]) {
                            $allDisposedDepreciation[] = $row;
                        }
                    }
                }

                //* Get All Year Depreciation
                $dataDepre = $depreciation->whereIn('assetcode', $assetList)->where('startyear >=', $yearDisposal)->findAll();

                $allYearDepreciation = [];
                foreach ($dataDepre as $row) {
                    $allYearDepreciation[$row->assetcode][] = $row;
                }

                //* Get All Sum Depreciation
                $dataDepre = $depreciationDetail->select('assetcode, SUM(costdepreciation) as total_cost, count(trx_depreciation_detail_id) as total_month')
                    ->whereIn('assetcode', $assetList)
                    ->where('costdepreciation !=', 0)
                    ->like('period', $yearDisposal)
                    ->groupBy('assetcode')
                    ->findAll();

                $allCostSumDepreciation = [];
                $allTotalMonthDepreciation = [];
                foreach ($dataDepre as $row) {
                    $allCostSumDepreciation[$row->assetcode] = $row->total_cost;
                    $allTotalMonthDepreciation[$row->assetcode] = $row->total_month;
                }

                $dataDepre = [];
                $processedDepreciationIds = [];
                foreach ($allDisposedDepreciation as $row) {
                    foreach ($allYearDepreciation[$row->assetcode] ?? [] as $val) {
                        if (isset($processedDepreciationIds[$val->trx_depreciation_id])) {
                            continue;
                        }

                        $isCurrentYear = $val->startyear == $yearDisposal;

                        $dataDepre[] = [
                            'trx_depreciation_id' => $val->trx_depreciation_id,
                            'costdepreciation' => $isCurrentYear ? ($allCostSumDepreciation[$val->assetcode] ?? 0) : 0,
                            'accumulateddepreciation' => $isCurrentYear ? $row->accumulateddepreciation : 0,
                            'bookvalue' => $isCurrentYear ? $row->bookvalue : 0,
                            'currentmonth' => $isCurrentYear ? ($allTotalMonthDepreciation[$val->assetcode] ?? 0) : 0,
                        ];

                        $processedDepreciationIds[$val->trx_depreciation_id] = true;
                    }
                }

                if (!empty($dataDepre))
                    $depreciation->updateBatch($dataDepre, 'trx_depreciation_id');

                // TODO : Update Inventory
                if (!empty($dataInventory))
                    $inventory->updateBatch($dataInventory, 'trx_inventory_id');
            }

            if ($this->entity->getState() == $this->DOCSTATUS_Completed && $table == 'trx_service') {
                $mServiceDetail = new M_ServiceDetail($this->request);

                $mServiceDetail->where($mServiceDetail->primaryKey, $recordLine_id)->set('md_status_id', 100004)->update();
            }
        }

        return $result;
    }

    public function create()
    {
        $mWe = new M_WEvent($this->request);
        $mUser = new M_User($this->request);
        $cMail = new Mail();
        $mMenu = new M_Menu($this->request);

        if ($this->request->getMethod(true) === 'POST') {
            $post = $this->request->getVar();
            $isAnswer = $post['isanswer'];
            $_ID = $post['record_id'];
            $txtMsg = $post['textmsg'];

            try {
                $activity = $this->model->find($_ID);
                $menuName = $mMenu->getMenuBy($activity->getMenu());

                if ($isAnswer === 'Y') {
                    $eList = $mWe->where($this->model->primaryKey, $_ID)->orderBy('created_at', 'ASC')->findAll();

                    foreach ($eList as $event) :
                        $this->wfResponsibleId[] = $event->getWfResponsibleId();
                    endforeach;

                    $this->wfScenarioId = $activity->getWfScenarioId();

                    $response = $this->setActivity($_ID, $activity->getWfScenarioId(), $activity->getWfResponsibleId(), $this->access->getSessionUser(), $this->DOCSTATUS_Completed, true, $txtMsg, $activity->getTable(), $activity->getRecordId(), $activity->getMenu(), $activity->getTableLine(), $activity->getRecordLineId());
                } else {
                    $response = $this->setActivity($_ID, $activity->getWfScenarioId(), $activity->getWfResponsibleId(), $this->access->getSessionUser(), $this->DOCSTATUS_Aborted, true, $txtMsg, $activity->getTable(), $activity->getRecordId(), $activity->getMenu(), $activity->getTableLine(), $activity->getRecordLineId());

                    $builder = $this->getBuilder($activity->getTable());
                    $builder->where($this->getPrimaryKey($activity->getTable()), $activity->getRecordId());
                    $sql = $builder->get()->getRow();
                    $subject = ucwords($menuName) . "_" . $sql->documentno;
                    $message =  'Tidak Di Approve' . "<br>";
                    $message .= "---" . "<br>";
                    $message .= ucwords($menuName) . " " . $sql->documentno . "<br>";

                    if (isset($sql->grandtotal))
                        $message .= "Approval Amount = " . formatRupiah($sql->grandtotal) . "<br>";

                    $message .= $sql->description;
                    $message = new Html2Text($message);
                    $message = $message->getText();

                    $user = $mUser->find($sql->created_by);
                    $cMail->sendEmail($user->email, $subject, $message, null, "SAS Asset");
                    $this->toForwardAlert('sys_wfresponsible', $activity->getWfResponsibleId(), $subject, $message);
                }

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
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return json_encode($response);
        }
    }

    private function getNextResponsible()
    {
        $mWfsD = new M_WScenarioDetail($this->request);
        $nextResp = 0;
        $responsible = [];

        $list = $mWfsD->where([
            'sys_wfscenario_id'       => $this->wfScenarioId,
            'isactive'                => 'Y'
        ])->orderBy('lineno', 'DESC')->findAll();

        foreach ($list as $key => $resp) :
            if (!in_array($resp->getWfResponsibleId(), $this->wfResponsibleId))
                $responsible[] = $resp->getWfResponsibleId();
        endforeach;

        if (!empty($responsible)) {
            $nextResp = end($responsible);
        }

        return $nextResp;
    }

    public function showNotif()
    {
        $list = $this->model->getActivity("count");
        return json_encode($list);
    }

    public function getBuilder($table)
    {
        return $this->model->db->table($table);
    }

    public function getPrimaryKey($table)
    {
        $fields = $this->model->db->getFieldData($table);

        $field = "";

        foreach ($fields as $row) :
            if ($row->primary_key == 1)
                $field = $row->name;
        endforeach;

        return $field;
    }

    public function doApproved()
    {
        $mMovement = new M_Movement($this->request);

        $this->session->set([
            'sys_user_id'       => 100000,
        ]);

        $where = 'ADDDATE(sys_wfactivity.created_at, INTERVAL 3 DAY) <= NOW()';
        $where .= " AND sys_wfactivity.table = 'trx_movement'";

        $list = $this->model->getActivity(null, $where);

        if ($list) {
            foreach ($list as $row) {
                $move = $mMovement->where([
                    'trx_movement_id'   => $row->record_id,
                    'docstatus'         => "{$this->DOCSTATUS_Inprogress}"
                ])->first();

                if ($move->movementtype === $this->Movement_Terima)
                    $this->setActivity($row->sys_wfactivity_id, $row->sys_wfscenario_id, $row->sys_wfresponsible_id, session()->get('sys_user_id'), $this->DOCSTATUS_Completed, true, "Approved By System", $row->table, $row->record_id, $row->menu);
            }
        }
    }
}
