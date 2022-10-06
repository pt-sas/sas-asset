<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Responsible;
use App\Models\M_Reference;
use App\Models\M_Role;
use App\Models\M_User;
use Config\Services;

class Responsible extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Responsible($this->request);
        $this->entity = new \App\Entities\Responsible();
    }

    public function index()
    {
        $reference = new M_Reference($this->request);

        $data = [
            'ref_list' => $reference->findBy([
                'sys_reference.name'              => 'WF_Participant Type',
                'sys_reference.isactive'          => 'Y',
                'sys_ref_detail.isactive'         => 'Y',
            ], null, [
                'field'     => 'sys_ref_detail.name',
                'option'    => 'ASC'
            ])->getResult()
        ];

        return $this->template->render('backend/configuration/responsible/v_responsible', $data);
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
                $ID = $value->sys_wfresponsible_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->name;
                $row[] = $value->description;
                $row[] = $value->res_type;
                $row[] = $value->role;
                $row[] = $value->user;
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

            try {
                $this->entity->fill($post);

                if ($post['responsibletype'] === 'R')
                    $this->entity->sys_user_id = 0;

                if ($post['responsibletype'] === 'H')
                    $this->entity->sys_role_id = 0;

                if (!$this->validation->run($post, 'responsible')) {
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
        $role = new M_Role($this->request);
        $user = new M_User($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();

                if (!empty($list[0]->sys_role_id)) {
                    $rowRole = $role->find($list[0]->sys_role_id);
                    $list = $this->field->setDataSelect($role->table, $list, $role->primaryKey, $rowRole->sys_role_id, $rowRole->name);
                }

                if (!empty($list[0]->sys_user_id)) {
                    $rowUser = $user->find($list[0]->sys_user_id);
                    $list = $this->field->setDataSelect($user->table, $list, $user->primaryKey, $rowUser->sys_user_id, $rowUser->name);
                }

                $result = [
                    'header'   => $this->field->store($this->model->table, $list)
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
}
