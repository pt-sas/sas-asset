<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_WScenario;
use App\Models\M_WScenarioDetail;
use App\Models\M_Menu;
use App\Models\M_NotificationText;
use App\Models\M_Responsible;
use App\Models\M_Status;
use Config\Services;

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
        $menu = new M_Menu($this->request);
        $status = new M_Status($this->request);

        $data = [
            'menu'      => $menu->getMenu(),
            'status'    => $status->where([
                'isactive'  => 'Y',
                'isline'    => 'N'
            ])->orderBy('name', 'ASC')
                ->findAll(),
        ];

        return $this->template->render('backend/configuration/wscenario/v_wscenario', $data);

        // $this->sys_wfscenario_id = 1;
        // $d = $this->getNextResponsible();
        // dd($d);
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
                $row[] = $value->description;
                $row[] = active($value->isactive);
                $row[] = $this->template->tableButton($ID);
                $data[] = $row;
            endforeach;

            $result = [
                'draw'              => $this->request->getPost('draw'),
                'recordsTotal'      => $this->datatable->countAll($table),
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
        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();
                $detail = $this->modelDetail->where($this->model->primaryKey, $id)->findAll();

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

        $trxLine = $this->modelDetail->where($primaryKey, $trxID)->first();

        if (!$trxLine && $docStatus === $this->DOCSTATUS_Completed) {
            $this->entity->setDocStatus($this->DOCSTATUS_Invalid);
            $this->entity->setWfScenarioId(0);
        } else if ($docStatus === $this->DOCSTATUS_Voided) {
            $this->entity->setDocStatus($this->DOCSTATUS_Voided);
        } else if ($trxLine && $docStatus === $this->DOCSTATUS_Completed) {
            $trx = $this->model->find($trxID);

            if ($table === 'trx_quotation') {
                $this->sys_wfscenario_id = $mWfs->getScenario($menu, $trx->getGroupAssetId(), $trx->getStatusId(), 0);

                if ($this->sys_wfscenario_id) {
                    $this->entity->setDocStatus($this->DOCSTATUS_Inprogress);
                    $this->entity->setWfScenarioId($this->sys_wfscenario_id);

                    $cWfa->setActivity(null, $this->sys_wfscenario_id, $this->getScenarioResponsible($this->sys_wfscenario_id), $sessionUserId, $this->DOCSTATUS_Suspended, false, null, $table, $trxID, $menu);
                } else {
                    $this->entity->setDocStatus($this->DOCSTATUS_Completed);
                    $this->entity->setWfScenarioId(0);
                }
            }
        }

        $this->entity->setUpdatedBy($session->get('sys_user_id'));
        $this->entity->{$primaryKey} = $trxID;
        return $this->save();
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
