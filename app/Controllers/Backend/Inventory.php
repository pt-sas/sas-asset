<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Datatable;
use App\Models\M_Inventory;
use App\Models\M_Product;
use App\Models\M_Branch;
use App\Models\M_Brand;
use App\Models\M_Employee;
use Config\Services;

class Inventory extends BaseController
{
    private $model;
    private $entity;
    protected $validation;
    protected $request;

    public function __construct()
    {
        $this->request = Services::request();
        $this->validation = Services::validation();
        $this->model = new M_Inventory($this->request);
        $this->entity = new \App\Entities\Inventory();
    }

    public function index()
    {
        return $this->template->render('transaction/inventory/v_inventory');
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
                $ID = $value->trx_inventory_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->assetcode;
                $row[] = $value->product;
                $row[] = $value->branch;
                $row[] = $value->division;
                $row[] = $value->room;
                $row[] = $value->employee;
                $row[] = $value->status;
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
        $employee = new M_Employee($this->request);

        if ($this->request->getMethod(true) === 'POST') {
            $post = $this->request->getVar();

            try {
                $this->entity->fill($post);
                $this->entity->setQtyEntered(1);

                // ROOM RUANG IT - BARANG BAGUS
                if ($post['md_room_id'] == 100040)
                    $this->entity->setIsSpare('Y');
                else if ($post['md_room_id'] == 100041) // ROOM RUANG IT - BARANG RUSAK
                    $this->entity->setIsSpare('N');
                else
                    $this->entity->setIsSpare('N');

                $this->entity->setIsActive(setCheckbox(isset($post['isactive'])));
                $this->entity->setCreatedBy($this->session->get('sys_user_id'));
                $this->entity->setUpdatedBy($this->session->get('sys_user_id'));

                if (!$this->validation->run($post, 'inventory')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    $row = $employee->find($post['md_employee_id']);
                    $this->entity->setDivisionId($row->getDivisionId());

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
        $product = new M_Product($this->request);
        $branch = new M_Branch($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();

                $rowProduct = $product->find($list[0]->getProductId());
                $rowBranch = $branch->find($list[0]->getBranchId());

                $list = $this->field->setDataSelect($product->table, $list, $product->primaryKey, $rowProduct->getProductId(), $rowProduct->getName());
                $list = $this->field->setDataSelect($branch->table, $list, $branch->primaryKey, $rowBranch->getBranchId(), $rowBranch->getName());

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
        $employee = new M_Employee($this->request);

        if ($this->request->getMethod(true) === 'POST') {
            $post = $this->request->getVar();

            try {
                $this->entity->fill($post);
                $this->entity->setInventoryId($post['id']);

                // ROOM RUANG IT - BARANG BAGUS
                if ($post['md_room_id'] == 100040)
                    $this->entity->setIsSpare('Y');
                else if ($post['md_room_id'] == 100041) // ROOM RUANG IT - BARANG RUSAK
                    $this->entity->setIsSpare('N');
                else
                    $this->entity->setIsSpare('N');

                $this->entity->setIsActive(setCheckbox(isset($post['isactive'])));
                $this->entity->setUpdatedBy($this->session->get('sys_user_id'));

                if (!$this->validation->run($post, 'inventory')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    $emp = $employee->find($post['md_employee_id']);
                    $this->entity->setDivisionId($emp->md_division_id);

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
}
