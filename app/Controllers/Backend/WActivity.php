<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_AlertRecipient;
use App\Models\M_Responsible;
use App\Models\M_User;
use App\Models\M_WActivity;
use App\Models\M_WEvent;
use App\Models\M_WScenarioDetail;
use Config\Services;
use Pusher\Pusher;
use Html2Text\Html2Text;

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
    }

    private function toForwardAlert($table, $record_id, $subject, $message)
    {
        $mAlert = new M_AlertRecipient($this->request);
        $cMail = new Mail();
        $alert = $mAlert->getAlertRecipient($table, $record_id);

        $result = false;

        foreach ($alert as $val) :
            if (!empty($val->email))
                $result = $cMail->sendEmail($val->email, $subject, $message, null, "SAS Asset");
        endforeach;

        return $result;
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
    }

    public function setActivity($sys_wfactivity_id, $sys_wfscenario_id, $sys_wfresponsible_id, $user_by, $state, $processed, $textmsg, $table, $record_id, $menu)
    {
        $mWr = new M_Responsible($this->request);
        $mWe = new M_WEvent($this->request);
        $mUser = new M_User($this->request);
        $cMail = new Mail();

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
            $result = $this->model->save($this->entity);

            $sys_wfactivity_id = $this->model->getInsertID();
            $s = $mWe->setEventAudit($sys_wfactivity_id, $sys_wfresponsible_id, $user_id, $state, $processed, $table, $record_id, $user_by);

            $resp = $mWr->find($sys_wfresponsible_id);
            $list = $mUser->detail(['sr.sys_role_id' => $resp->getRoleId()])->getResult();

            $builder = $this->getBuilder($table);
            $builder->where($this->getPrimaryKey($table), $record_id);
            $sql = $builder->get()->getRow();
            $subject = ucwords($menu) . "_" . $sql->documentno;
            $message =  '<p>Dear Mr/Ms,</p><p><span style="letter-spacing: 0.05em;">Please approve document below.</span></p><div><br></div>';
            $message .= "-----" . " " . ucwords($menu) . " ";
            $message .= $sql->documentno . ": Approval Amount =" . $sql->grandtotal;
            $message = new Html2Text($message);
            $message = $message->getText();

            foreach ($list as $key => $user) :
                $cMail->sendEmail($user->email, $subject, $message, null, "SAS Asset");
            endforeach;

            $this->toForwardAlert('sys_wfresponsible', $sys_wfresponsible_id, $subject, $message);
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

                $resp = $mWr->find($sys_wfresponsible_id);
                $list = $mUser->detail(['sr.sys_role_id' => $resp->getRoleId()])->getResult();

                $builder = $this->getBuilder($table);
                $builder->where($this->getPrimaryKey($table), $record_id);
                $sql = $builder->get()->getRow();
                $subject = ucwords($menu) . "_" . $sql->documentno;
                $message =  '<p>Dear Mr/Ms,</p><p><span style="letter-spacing: 0.05em;">Please approve document below.</span></p><div><br></div>';
                $message .= "-----" . " " . ucwords($menu) . " ";
                $message .= $sql->documentno . ": Approval Amount =" . $sql->grandtotal;
                $message = new Html2Text($message);
                $message = $message->getText();

                foreach ($list as $key => $user) :
                    $cMail->sendEmail($user->email, $subject, $message, null, "SAS Asset");
                endforeach;

                $this->toForwardAlert('sys_wfresponsible', $sys_wfresponsible_id, $subject, $message);
            } else {
                $builder = $this->model->db->table($table);

                if ($state === $this->DOCSTATUS_Aborted && $processed) {
                    $s = $mWe->setEventAudit($sys_wfactivity_id, $sys_wfresponsible_id, $user_id, $state, $processed, $table, $record_id, $user_by);

                    $data = [
                        'docstatus' => $this->DOCSTATUS_NotApproved
                    ];

                    $builder->where($this->getPrimaryKey($table), $record_id)->update($data);
                } else {
                    $state = $this->DOCSTATUS_Completed;
                    $processed = true;
                    $s = $mWe->setEventAudit($sys_wfactivity_id, $sys_wfresponsible_id, $user_id, $state, $processed, $table, $record_id, $user_by);

                    $data = [
                        'docstatus' => $state
                    ];

                    $builder->where($this->getPrimaryKey($table), $record_id)->update($data);
                }

                $builder = $this->getBuilder($table);
                $builder->where($this->getPrimaryKey($table), $record_id);
                $sql = $builder->get()->getRow();
                $subject = ucwords($menu) . "_" . $sql->documentno;
                $message =  'Sudah Di Approve' . "<br>";
                $message .= "---" . "<br>";
                $message .= ucwords($menu) . " " . $sql->documentno . "<br>";
                $message .= "Approval Amount = " . $sql->grandtotal . "<br>";
                $message .= $sql->description;
                $message = new Html2Text($message);
                $message = $message->getText();

                $user = $mUser->find($sql->created_by);
                $cMail->sendEmail($user->email, $subject, $message, null, "SAS Asset");
            }

            $this->entity->setWfResponsibleId($sys_wfresponsible_id);
            $this->entity->setSysUserId($user_id);
            $this->entity->setState($state);
            $this->entity->setProcessed($processed);
            $this->entity->setUpdatedBy($user_by);
            $this->entity->setWfActivityId($sys_wfactivity_id);
            $result = $this->save($this->entity);
        }

        return $result;
    }

    public function create()
    {
        $mWe = new M_WEvent($this->request);
        $mUser = new M_User($this->request);
        $cMail = new Mail();

        if ($this->request->getMethod(true) === 'POST') {
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
                    $response = $s;
                } else {
                    $s = $this->setActivity($_ID, $activity->getWfScenarioId(), $activity->getWfResponsibleId(), $this->access->getSessionUser(), $this->DOCSTATUS_Aborted, true, $txtMsg, $activity->getTable(), $activity->getRecordId(), $activity->getMenu());
                    $response = 'aborted';

                    $builder = $this->getBuilder($activity->getTable());
                    $builder->where($this->getPrimaryKey($activity->getTable()), $activity->getRecordId());
                    $sql = $builder->get()->getRow();
                    $subject = ucwords($activity->getMenu()) . "_" . $sql->documentno;
                    $message =  'Sudah Di Approve' . "<br>";
                    $message .= "---" . "<br>";
                    $message .= ucwords($activity->getMenu()) . " " . $sql->documentno . "<br>";
                    $message .= "Approval Amount = " . $sql->grandtotal . "<br>";
                    $message .= $sql->description;
                    $message = new Html2Text($message);
                    $message = $message->getText();

                    $user = $mUser->find($sql->created_by);
                    $cMail->sendEmail($user->email, $subject, $message, null, "SAS Asset");
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
        $list = $this->model->countData();
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
}
