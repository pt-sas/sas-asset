<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Responsible;
use App\Models\M_User;
use App\Models\M_UserRole;
use App\Models\M_WActivity;
use App\Models\M_WEvent;
use App\Models\M_WScenarioDetail;
use Config\Services;

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

    public function index()
    {
        // $r = $this->create('Y', 1);
        $r = $this->setActivity(null, 1, 1, 1, 'OS', false, null, 'trx_quotation', 3, 'quotation');
        dd($r);
    }

    public function showActivityInfo()
    {
        if ($this->request->isAjax()) {
            $data = [];
            $list = $this->model->getActivity();

            foreach ($list as $value) :
                $row = [];
                $ID = $value->sys_wfactivity_id;
                $record_id = $value->record_id;
                $table = $value->table;
                $menu = $value->menu;
                $node = 'Approval ' . ucwords($menu);
                $summary = ucwords($menu) . ' ' . $value->documentno . ': ' . $value->usercreated_by;

                $row[] = $ID;
                $row[] = $record_id;
                $row[] = $table;
                $row[] = $menu;
                $row[] = $node;
                $row[] = $summary;
                $data[] = $row;
            endforeach;

            $result = [
                'data'              => $data
            ];

            return $this->response->setJSON($result);
        }

        // return $list;
    }

    public function setActivity($sys_wfactivity_id, $sys_wfscenario_id, $sys_wfresponsible_id, $user_by, $state, $processed, $textmsg, $table, $record_id, $menu)
    {
        $mWr = new M_Responsible($this->request);
        $mWe = new M_WEvent($this->request);
        $mUser = new M_User($this->request);

        $this->entity->setWfScenarioId($sys_wfscenario_id);

        $this->entity->setTable($table);
        $this->entity->setRecordId($record_id);
        $this->entity->setMenu($menu);
        $user_id = $mWr->getUserByResponsible($sys_wfresponsible_id);

        if (empty($sys_wfactivity_id)) {
            $this->entity->setWfResponsibleId($sys_wfresponsible_id);
            $this->entity->setSysUserId($user_id);
            $this->entity->setState($state);
            $this->entity->setTextMsg($textmsg);
            $this->entity->setProcessed($processed);
            $this->entity->setCreatedBy($user_by);
            $this->entity->setUpdatedBy($user_by);
            $result = $this->save($this->entity);

            $sys_wfactivity_id = $this->model->getInsertID();
            $s = $mWe->setEventAudit($sys_wfactivity_id, $sys_wfresponsible_id, $user_id, $state, $processed, $table, $record_id, $user_by);
        } else {
            if (!empty($this->getNextResponsible())) {
                $newWfResponsibleId = $this->getNextResponsible();
                $user_id = $mWr->getUserByResponsible($newWfResponsibleId);

                $s = $mWe->setEventAudit($sys_wfactivity_id, $sys_wfresponsible_id, $user_id, $state, $processed, $table, $record_id, $user_by, true);

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
                    $s = $mWe->setEventAudit($sys_wfactivity_id, $sys_wfresponsible_id, $user_id, $state, $processed, $table, $record_id, $user_by);
                }
                // } else {
                // $s = $mWe->setEventAudit($sys_wfactivity_id, $sys_wfresponsible_id, $user_id, $state, $processed, $table, $record_id, $user_by);
                // $this->entity->setWfResponsibleId($sys_wfresponsible_id);
            }

            if ($state === $this->DOCSTATUS_Aborted && $processed) {
                $s = $mWe->setEventAudit($sys_wfactivity_id, $sys_wfresponsible_id, $user_id, $state, $processed, $table, $record_id, $user_by);
            }

            // $s = $mWe->setEventAudit($sys_wfactivity_id, $sys_wfresponsible_id, $user_id, $state, $processed, $table, $record_id, $user_by);

            $this->entity->setWfResponsibleId($sys_wfresponsible_id);
            $this->entity->setSysUserId($user_id);
            $this->entity->setState($state);
            $this->entity->setProcessed($processed);
            $this->entity->setUpdatedBy($user_by);
            $this->entity->setWfActivityId($sys_wfactivity_id);
            $result = $this->save($this->entity);
        }

        return $result;
        // return $this->entity;
        // return $sys_wfresponsible_id;
    }

    public function create()
    {
        $mWe = new M_WEvent($this->request);
        // if ($this->request->getMethod(true) === 'POST') {
        $post = $this->request->getVar();
        $isAnswer = $post['isanswer'];
        $_ID = $post['id'];
        $txtMsg = $post['textmsg'];

        try {
            $activity = $this->model->find($_ID);

            if ($isAnswer === 'Y') {
                $eList = $mWe->where($this->model->primaryKey, $_ID)->orderBy('created_at', 'ASC')->findAll();

                foreach ($eList as $event) :
                    $this->wfResponsibleId[] = $event->getWfResponsibleId();
                endforeach;

                $this->wfScenarioId = $activity->getWfScenarioId();

                $s = $this->setActivity($_ID, $activity->getWfScenarioId(), $activity->getWfResponsibleId(), $this->access->getSessionUser(), $this->DOCSTATUS_Completed, true, $txtMsg, $activity->getTable(), $activity->getRecordId(), $activity->getMenu());
                // $s = $this->setActivity($_ID, $activity->getWfScenarioId(), $activity->getWfResponsibleId(), 100160, $this->DOCSTATUS_Completed, true, $txtMsg, $activity->getTable(), $activity->getRecordId(), $activity->getMenu());
                $response = $s;
            } else {
                $s = $this->setActivity($_ID, $activity->getWfScenarioId(), $activity->getWfResponsibleId(), $this->access->getSessionUser(), $this->DOCSTATUS_Aborted, true, $txtMsg, $activity->getTable(), $activity->getRecordId(), $activity->getMenu());
                $response = 'aborted';
            }
        } catch (\Exception $e) {
            $response = message('error', false, $e->getMessage());
        }

        // return $this->response->setJSON($response);

        return json_encode($response);
        // return $response;
        // }
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
}
