<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_WScenario;
use App\Models\M_WScenarioDetail;
use App\Models\M_Menu;
use App\Models\M_NotificationText;
use App\Models\M_Responsible;
use App\Models\M_Status;
use App\Models\M_Branch;
use App\Models\M_Room;
use App\Models\M_Division;
use App\Models\M_Inventory;
use App\Models\M_Reference;
use App\Models\M_Transaction;
use Config\Services;
use Pusher\Pusher;
use stdClass;

class WScenario extends BaseController
{
    protected $sys_wfscenario_id = 0;

    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_WScenario($this->request);
        $this->modelDetail = new M_WScenarioDetail($this->request);
        $this->entity = new \App\Entities\WScenario();
    }

    public function index()
    {
        $uri = $this->request->uri->getSegment(2);
        $menu = new M_Menu($this->request);
        $status = new M_Status($this->request);
        $mRef = new M_Reference($this->request);

        $data = [
            'menu'      => $menu->getMenuUrl(),
            'ref_list' => $mRef->findBy(
                "sys_reference.name IN ('MovementType','DisposalType', 'QuotationType') 
                AND sys_reference.isactive = 'Y' 
                AND sys_ref_detail.isactive = 'Y'",
                null,
                [
                    'field'     => 'sys_ref_detail.sys_ref_detail_id',
                    'option'    => 'ASC'
                ]
            )->getResult(),
            'status'    => $status->where([
                'isactive'  => 'Y',
                'isline'    => 'N'
            ])->like('menu_id', $uri)
                ->orderBy('name', 'ASC')
                ->findAll()
        ];

        return $this->template->render('backend/configuration/wscenario/v_wscenario', $data);
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

            $data = [];

            $number = $this->request->getPost('start');
            $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search, $join);

            foreach ($list as $value) :
                $row = [];
                $ID = $value->sys_wfscenario_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->name;
                $row[] = $value->lineno;
                $row[] = $value->grandtotal;
                $row[] = $value->menu;
                $row[] = $value->status;
                $row[] = $value->branch;
                $row[] = $value->division;
                $row[] = $value->scenariotype;
                $row[] = $value->description;
                $row[] = active($value->isactive);
                $row[] = $this->template->tableButton($ID);
                $data[] = $row;
            endforeach;

            $result = [
                'draw'              => $this->request->getPost('draw'),
                'recordsTotal'      => $this->datatable->countAll($table, $select, $order, $sort, $search),
                'recordsFiltered'   => $this->datatable->countFiltered($table, $select, $order, $sort, $search, $join),
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

                if (!$this->validation->run($post, 'wscenario')) {
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
        $branch = new M_Branch($this->request);
        $division = new M_Division($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();
                $detail = $this->modelDetail->where($this->model->primaryKey, $id)->findAll();

                if (!empty($list[0]->getBranchId())) {
                    $rowBranch = $branch->find($list[0]->getBranchId());
                    $list = $this->field->setDataSelect($branch->table, $list, $branch->primaryKey, $rowBranch->getBranchId(), $rowBranch->getName());
                }

                if (!empty($list[0]->getDivisionId())) {
                    $rowDivision = $division->find($list[0]->getDivisionId());
                    $list = $this->field->setDataSelect($division->table, $list, $division->primaryKey, $rowDivision->getDivisionId(), $rowDivision->getName());
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

    public function tableLine($set = null, $detail = [])
    {
        $responsible = new M_Responsible($this->request);
        $notifTemp = new M_NotificationText($this->request);

        $dataNotif = $notifTemp->where('isactive', 'Y')
            ->orderBy('name', 'ASC')
            ->findAll();

        $dataRespon = $responsible->where('isactive', 'Y')
            ->orderBy('name', 'ASC')
            ->findAll();

        $table = [];

        //? Create
        if (empty($set)) {
            $table = [
                $this->field->fieldTable('input', 'text', 'lineno', 'number', null, null, null, null, 0, 70),
                $this->field->fieldTable('input', 'text', 'grandtotal', 'number', null, null, null, null, 0, 150),
                $this->field->fieldTable('select', null, 'sys_wfresponsible_id', null, 'required', null, null, $dataRespon, null, 200, 'sys_wfresponsible_id', 'name'),
                $this->field->fieldTable('select', null, 'sys_notiftext_id', null, 'required', null, null, $dataNotif, null, 200, 'sys_notiftext_id', 'name'),
                $this->field->fieldTable('input', 'checkbox', 'isactive', 'active', null, null, 'checked'),
                $this->field->fieldTable('button', 'button', 'sys_wfscenario_detail_id')
            ];
        }

        //? Update
        if (!empty($set) && count($detail) > 0) {
            foreach ($detail as $row) :
                $table[] = [
                    $this->field->fieldTable('input', 'text', 'lineno', 'number', null, $row->isactive === 'N' ? 'readonly' : null, null, null, $row->lineno, 70),
                    $this->field->fieldTable('input', 'text', 'grandtotal', 'number', null, $row->isactive === 'N' ? 'readonly' : null, null, null, $row->grandtotal, 150),
                    $this->field->fieldTable('select', null, 'sys_wfresponsible_id', null, 'required', $row->isactive === 'N' ? 'readonly' : null, null, $dataRespon, $row->sys_wfresponsible_id, 200, 'sys_wfresponsible_id', 'name'),
                    $this->field->fieldTable('select', null, 'sys_notiftext_id', null, 'required', $row->isactive === 'N' ? 'readonly' : null, null, $dataNotif, $row->sys_notiftext_id, 200, 'sys_notiftext_id', 'name'),
                    $this->field->fieldTable('input', 'checkbox', 'isactive', 'active', null, null, null, null, $row->isactive),
                    $this->field->fieldTable('button', 'button', 'sys_wfscenario_detail_id', null, null, null, null, null, $row->sys_wfscenario_detail_id)
                ];
            endforeach;
        }

        return json_encode($table);
    }

    public function setScenario($entity, $model, $modelDetail, $trxID, $docStatus, $menu, $session)
    {
        $mWfs = new M_WScenario($this->request);
        $cWfa = new WActivity();

        $this->model = $model;
        $this->entity = $entity;
        $this->modelDetail = $modelDetail;

        $table = $this->model->table;
        $primaryKey = $this->model->primaryKey;
        $sessionUserId = $session->get('sys_user_id');
        $isWfscenario = false;

        $trxLine = $this->modelDetail->where($primaryKey, $trxID)->first();

        if (!$trxLine && $docStatus === $this->DOCSTATUS_Completed) {
            $this->entity->setDocStatus($this->DOCSTATUS_Invalid);
            $this->entity->setWfScenarioId(0);
        } else if ($docStatus === $this->DOCSTATUS_Voided) {
            $this->entity->setDocStatus($this->DOCSTATUS_Voided);
        } else if ($trxLine && $docStatus === $this->DOCSTATUS_Completed) {
            $trx = $this->model->find($trxID);

            if ($table === 'trx_quotation') {
                if (empty($trx->getQuotationType()))
                    $this->sys_wfscenario_id = $mWfs->getScenario($menu, null, $trx->getStatusId());
                else
                    $this->sys_wfscenario_id = $mWfs->getScenario($menu, null, $trx->getStatusId(), null, null, $trx->getQuotationType());

                if ($this->sys_wfscenario_id) {
                    $this->entity->setDocStatus($this->DOCSTATUS_Inprogress);
                    $this->entity->setWfScenarioId($this->sys_wfscenario_id);
                    $isWfscenario = true;
                } else {
                    $this->entity->setDocStatus($this->DOCSTATUS_Completed);
                }
            }

            if ($table === 'trx_movement') {
                $this->sys_wfscenario_id = $mWfs->getScenario($menu, null, null, $trx->md_branch_id, $trx->md_division_id, $trx->getMovementType());

                if ($this->sys_wfscenario_id) {
                    $this->entity->setDocStatus($this->DOCSTATUS_Inprogress);
                    $this->entity->setWfScenarioId($this->sys_wfscenario_id);
                    $isWfscenario = true;
                } else {
                    $this->entity->setDocStatus($this->DOCSTATUS_Completed);
                    $this->entity->setWfScenarioId(0);

                    $line = $this->modelDetail->where($primaryKey, $trxID)->findAll();
                    $inventory = new M_Inventory($this->request);
                    $transaction = new M_Transaction();

                    $arrMoveIn = [];
                    $arrMoveOut = [];

                    $data = [
                        'isaccept'                  => "Y",
                        'updated_at'                => date('Y-m-d H:i:s'),
                        'updated_by'                => session()->get('sys_user_id')
                    ];

                    $this->modelDetail->builder->where($primaryKey, $trxID)->update($data);

                    foreach ($line as $key => $value) :
                        //? Data movement from
                        $arrOut = new stdClass();
                        $room = new M_Room($this->request);
                        $transit = $room->where("name", "TRANSIT")->first();

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
        }

        $this->entity->setUpdatedBy($session->get('sys_user_id'));
        $this->entity->{$primaryKey} = $trxID;
        $result = $this->model->save($this->entity);

        if ($result && $isWfscenario) {
            $result = $cWfa->setActivity(null, $this->sys_wfscenario_id, $this->getScenarioResponsible($this->sys_wfscenario_id), $sessionUserId, $this->DOCSTATUS_Suspended, false, null, $table, $trxID, $menu);

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

        return $result;
    }

    private function getScenarioResponsible($sys_wfscenario_id)
    {
        $mWfsDetail = new M_WScenarioDetail($this->request);

        $row = $mWfsDetail->where([
            'sys_wfscenario_id'       => $sys_wfscenario_id,
            'isactive'                => 'Y'
        ])->orderBy('lineno', 'ASC')->first();

        return $row->getWfResponsibleId();
    }
}
