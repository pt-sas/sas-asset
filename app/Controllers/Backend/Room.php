<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Datatable;
use App\Models\M_Room;
use App\Models\M_Branch;
use App\Models\M_Employee;
use Config\Services;

class Room extends BaseController
{
    private $model;
    private $entity;
    protected $validation;
    protected $request;

    public function __construct()
    {
        $this->request = Services::request();
        $this->validation = Services::validation();
        $this->model = new M_Room($this->request);
        $this->entity = new \App\Entities\Room();
    }

    public function index()
    {
        return $this->template->render('masterdata/room/v_room');
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
                $ID = $value->md_room_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->value;
                $row[] = $value->name;
                $row[] = $value->branch;
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

                if (!$this->validation->run($post, 'room')) {
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
        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();

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

    public function edit()
    {
        if ($this->request->getMethod(true) === 'POST') {
            $post = $this->request->getVar();

            try {
                $this->entity->fill($post);
                $this->entity->setRoomId($post['id']);
                $this->entity->setIsActive(setCheckbox(isset($post['isactive'])));
                $this->entity->setUpdatedBy($this->session->get('sys_user_id'));

                if (!$this->validation->run($post, 'room')) {
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

                $docno = "RM" . $number;

                $response = message('success', true, $docno);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function getList()
    {
        $employee = new M_Employee($this->request);

        if ($this->request->isAjax()) {
            $post = $this->request->getVar();

            $response = [];

            try {
                if (!empty($post['reference'])) {
                    // condition post contain is numeric
                    if (preg_match('~[0-9]+~', $post['reference'])) {
                        if (isset($post['key']) && !empty($post['key'])) {
                            if ($post['key'] == 'all')
                                $list = $this->model->where([
                                    'isactive'      => 'Y',
                                    'md_branch_id'  => $post['reference']
                                ])->orderBy('name', 'ASC')->findAll();
                            else
                                $list = $this->model->where([
                                    'isactive'      => 'Y',
                                    'value <>'      => 'RM00041',
                                    'md_branch_id'  => $post['reference']
                                ])->orderBy('name', 'ASC')->findAll();
                        } else {
                            $value = $employee->find($post['reference']);

                            $list = $this->model->where([
                                'isactive'      => 'Y',
                                'value <>'      => 'RM00041',
                                'md_branch_id'  => $value->getBranchId()
                            ])->orderBy('name', 'ASC')->findAll();
                        }
                    } else {
                        if ($post['reference'] === 'IT') {
                            /**
                             * RM00039 => IT
                             * RM00040 => RUANG IT - BARANG BAGUS
                             * RM00041 => RUANG IT - BARANG RUSAK
                             */
                            $ROOM_IT = ['RM00039', 'RM00040'];

                            $list = $this->model->where('isactive', 'Y')
                                ->whereIn('value', $ROOM_IT)
                                ->orderBy('name', 'ASC')
                                ->findAll();
                        }
                    }
                } else {
                    $list = $this->model->where('isactive', 'Y')
                        ->orderBy('name', 'ASC')
                        ->findAll();
                }

                foreach ($list as $key => $row) :
                    $response[$key]['id'] = $row->getRoomId();
                    $response[$key]['text'] = $row->getName();

                    if (!empty($post['reference']) && preg_match('~[0-9]+~', $post['reference']) && !isset($post['key']))
                        $response[$key]['key'] = $value->getRoomId();
                endforeach;
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }
}
