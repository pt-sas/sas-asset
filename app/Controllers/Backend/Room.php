<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Room;
use App\Models\M_Branch;
use App\Models\M_Employee;
use App\Models\M_User;
use Config\Services;

class Room extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Room($this->request);
        $this->entity = new \App\Entities\Room();
    }

    public function index()
    {
        return $this->template->render('masterdata/room/v_room');
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
                $ID = $value->md_room_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->value;
                $row[] = $value->name;
                $row[] = $value->branch;
                $row[] = $value->userrep;
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

            try {
                $this->entity->fill($post);

                if (!$this->validation->run($post, 'room')) {
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
        $user = new M_User($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();

                if (!empty($list[0]->getBranchId())) {
                    $rowBranch = $branch->find($list[0]->getBranchId());

                    $list = $this->field->setDataSelect($branch->table, $list, $branch->primaryKey, $rowBranch->getBranchId(), $rowBranch->getName());
                }

                if (!empty($list[0]->getUserRepId())) {
                    $rowUser = $user->find($list[0]->getUserRepId());

                    $list = $this->field->setDataSelect($user->table, $list, 'userrep_id', $rowUser->getUserId(), $rowUser->getName());
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
                if (isset($post['search'])) {
                    if (!empty($post['name'])) {
                        $list = $this->model->where('isactive', 'Y')
                            ->like('name', $post['search'])
                            ->like('name', $post['name'])
                            ->orderBy('name', 'ASC')
                            ->findAll();
                    } else {
                        $list = $this->model->where('isactive', 'Y')
                            ->like('name', $post['search'])
                            ->orderBy('name', 'ASC')
                            ->findAll();
                    }
                } else if (isset($post['name'])) {
                    $list = $this->model->where('isactive', 'Y')
                        ->like('name', $post['name'])
                        ->orderBy('name', 'ASC')
                        ->findAll();
                } else if (!empty($post['reference'])) {
                    // condition post contain is numeric
                    if (preg_match('~[0-9]+~', $post['reference'])) {
                        if (isset($post['key']) && !empty($post['key'])) {
                            $list = $this->model->where([
                                'isactive'      => 'Y',
                                'md_branch_id'  => $post['reference']
                            ])->orderBy('name', 'ASC')->findAll();
                        } else {
                            $value = $employee->find($post['reference']);

                            $list = $this->model->where([
                                'isactive'      => 'Y',
                                'md_branch_id'  => $value->getBranchId()
                            ])->orderBy('name', 'ASC')->findAll();
                        }
                    }
                } else {
                    $list = $this->model->where('isactive', 'Y')
                        ->orderBy('name', 'ASC')
                        ->findAll();
                }

                foreach ($list as $key => $row) :
                    $response[$key]['id'] = $row->getRoomId();
                    $response[$key]['text'] = $row->getName() . " (" . $row->getDescription() . ")";

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
