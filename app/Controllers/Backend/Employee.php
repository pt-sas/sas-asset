<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Datatable;
use App\Models\M_Employee;
use App\Models\M_Branch;
use App\Models\M_Division;
use App\Models\M_User;
use Config\Services;

class Employee extends BaseController
{
    private $model;
    private $entity;
    protected $validation;
    protected $request;

    public function __construct()
    {
        $this->request = Services::request();
        $this->validation = Services::validation();
        $this->model = new M_Employee($this->request);
        $this->entity = new \App\Entities\Employee();
    }

    public function index()
    {
        $user = new M_User($this->request);

        $data = [
            'user'        => $user->where('isactive', 'Y')
                ->orderBy('name', 'ASC')
                ->findAll()
        ];

        return $this->template->render('masterdata/employee/v_employee', $data);
    }

    public function showAll()
    {
        $datatable = new M_Datatable($this->request);

        if ($this->request->getMethod(true) === 'POST') {
            $table = $this->model->table;
            $select = $this->model->getSelect();
            $join = $this->model->getJoin();
            $order = $this->model->column_order;
            $sort = $this->model->order;
            $search = $this->model->column_search;

            $data = [];

            $number = $this->request->getPost('start');
            $list = $datatable->getDatatables($table, $select, $order, $sort, $search, $join);

            foreach ($list as $value) :
                $row = [];
                $ID = $value->md_employee_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->value;
                $row[] = $value->name;
                $row[] = $value->branch;
                $row[] = $value->division;
                $row[] = $value->room;
                $row[] = active($value->isactive);
                $row[] = $this->template->tableButton($ID);
                $data[] = $row;
            endforeach;

            $result = [
                'draw'              => $this->request->getPost('draw'),
                'recordsTotal'      => $datatable->countAll($table),
                'recordsFiltered'   => $datatable->countFiltered($table, $select, $order, $sort, $search, $join),
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
                $this->entity->setIsActive(setCheckbox(isset($post['isactive'])));
                $this->entity->setCreatedBy($this->session->get('sys_user_id'));
                $this->entity->setUpdatedBy($this->session->get('sys_user_id'));

                if (!$this->validation->run($post, 'employee')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    $result = $this->model->save($this->entity);

                    $msg = $result ? notification('insert') : $result;

                    $response = message('success', true, $msg);
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
                $list = $this->model->detail([], $this->model->table . '.' . $this->model->primaryKey, $id);

                $arrList = $list->getResult();
                $rowBranch = $branch->find($arrList[0]->md_branch_id);
                $rowDivision = $division->find($arrList[0]->md_division_id);

                $arrList = $this->field->setDataSelect($branch->table, $list->getResult(), $branch->primaryKey, $rowBranch->getBranchId(), $rowBranch->getName());
                $arrList = $this->field->setDataSelect($division->table, $list->getResult(), $division->primaryKey, $rowDivision->getDivisionId(), $rowDivision->getName());

                $result = [
                    'header'   => $this->field->store($this->model->table, $arrList, $list)
                ];

                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function edit()
    {
        if ($this->request->getMethod(true) === 'POST') {
            $post = $this->request->getVar();

            try {
                $this->entity->fill($post);
                $this->entity->setEmployeeId($post['id']);
                $this->entity->setIsActive(setCheckbox(isset($post['isactive'])));
                $this->entity->setUpdatedBy($this->session->get('sys_user_id'));

                if (!$this->validation->run($post, 'employee')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    $result = $this->model->save($this->entity);

                    $msg = $result ? notification('update') : $result;

                    $response = message('success', true, $msg);
                }
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

    public function getSeqCode()
    {
        if ($this->request->isAJAX()) {
            try {
                $number = $this->model->countAll();

                $number += 1;
                while (strlen($number) < 5) {
                    $number = "0" . $number;
                }

                $docno = "EM" . $number;

                $response = message('success', true, $docno);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function getList()
    {
        if ($this->request->isAjax()) {
            $post = $this->request->getVar();

            $response = [];

            try {
                if (isset($post['search'])) {
                    $list = $this->model->where('isactive', 'Y')
                        ->like('name', $post['search'])
                        ->orderBy('name', 'ASC')
                        ->findAll();
                } else if (!empty($post['reference'])) {
                    if ($post['reference'] == 100040 || $post['reference'] == 100040) {
                        $list = $this->model->where([
                            'isactive'          => 'Y',
                            'md_employee_id'    => 100130
                        ])->orderBy('name', 'ASC')->findAll();
                    } else {
                        $list = $this->model->where([
                            'isactive'      => 'Y',
                            'md_room_id'  => $post['reference']
                        ])->orderBy('name', 'ASC')->findAll();

                        // Employee not exist in the room
                        if (count($list) == 0 && !empty($post['branch'])) {
                            $list = $this->model->where([
                                'isactive'      => 'Y',
                                'md_branch_id'  => $post['branch']
                            ])->orderBy('name', 'ASC')->findAll();
                        }
                    }
                } else {
                    $list = $this->model->where('isactive', 'Y')
                        ->orderBy('name', 'ASC')
                        ->findAll();
                }

                foreach ($list as $key => $row) :
                    $response[$key]['id'] = $row->getEmployeeId();
                    $response[$key]['text'] = $row->getName();
                endforeach;
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }
}
