<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Datatable;
use App\Models\M_Supplier;
use Config\Services;

class Supplier extends BaseController
{
    private $model;
    private $entity;
    protected $validation;
    protected $request;

    public function __construct()
    {
        $this->request = Services::request();
        $this->validation = Services::validation();
        $this->model = new M_Supplier($this->request);
        $this->entity = new \App\Entities\Supplier();
    }

    public function index()
    {
        return $this->template->render('masterdata/supplier/v_supplier');
    }

    public function showAll()
    {
        $datatable = new M_Datatable($this->request);

        if ($this->request->getMethod(true) === 'POST') {
            $table = $this->model->table;
            $select = $this->model->findAll();
            $order = $this->model->column_order;
            $sort = $this->model->order;
            $search = $this->model->column_search;

            $data = [];

            $number = $this->request->getPost('start');
            $list = $datatable->getDatatables($table, $select, $order, $sort, $search);

            foreach ($list as $value) :
                $row = [];
                $ID = $value->md_supplier_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->value;
                $row[] = $value->name;
                $row[] = $value->email;
                $row[] = $value->address;
                $row[] = $value->owner;
                $row[] = $value->phone;
                $row[] = status($value->isvendor);
                $row[] = status($value->isservice);
                $row[] = active($value->isactive);
                $row[] = $this->template->tableButton($ID);
                $data[] = $row;
            endforeach;

            $result = [
                'draw'              => $this->request->getPost('draw'),
                'recordsTotal'      => $datatable->countAll($table),
                'recordsFiltered'   => $datatable->countFiltered($table, $select, $order, $sort, $search),
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
                $this->entity->setIsVendor(setCheckbox(isset($post['isvendor'])));
                $this->entity->setIsService(setCheckbox(isset($post['isservice'])));
                $this->entity->setCreatedBy($this->session->get('sys_user_id'));
                $this->entity->setUpdatedBy($this->session->get('sys_user_id'));

                if (!$this->validation->run($post, 'supplier')) {
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
                    'header'    => $this->field->store($this->model->table, $list)
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
                $this->entity->setSupplierId($post['id']);
                $this->entity->setIsActive(setCheckbox(isset($post['isactive'])));
                $this->entity->setIsVendor(setCheckbox(isset($post['isvendor'])));
                $this->entity->setIsService(setCheckbox(isset($post['isservice'])));
                $this->entity->setUpdatedBy($this->session->get('sys_user_id'));

                if (!$this->validation->run($post, 'supplier')) {
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

                $docno = "SP" . $number;

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
                    $list = $this->model->where([
                        'isactive'  => 'Y',
                        'isvendor'  => 'Y'
                    ])->like('name', $post['search'])
                        ->orderBy('name', 'ASC')
                        ->findAll();
                } else if (isset($post['field']) && isset($post['reference'])) {
                    $list = $this->model->where([
                        'isactive'      => 'Y',
                        $post['field']  => $post['reference']
                    ])->orderBy('name', 'ASC')
                        ->findAll();
                } else {
                    $list = $this->model->where([
                        'isactive'  => 'Y',
                        'isvendor'  => 'Y'
                    ])->orderBy('name', 'ASC')
                        ->findAll(5);
                }

                foreach ($list as $key => $row) :
                    $response[$key]['id'] = $row->getSupplierId();
                    $response[$key]['text'] = $row->getName();
                endforeach;
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }
}
