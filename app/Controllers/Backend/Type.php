<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Type;
use App\Models\M_Subcategory;
use Config\Services;

class Type extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Type($this->request);
        $this->entity = new \App\Entities\Type();
    }

    public function index()
    {
        return $this->template->render('masterdata/type/v_type');
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
                $ID = $value->md_type_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->value;
                $row[] = $value->name;
                $row[] = $value->subcategory;
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

                if (!$this->validation->run($post, 'type')) {
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
        $sub = new M_Subcategory($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();

                if (!empty($list[0]->getSubCategoryId())) {
                    $rowSub = $sub->getListSub($sub->table . '.' . $sub->primaryKey, $list[0]->getSubCategoryId())->getRow();

                    $list = $this->field->setDataSelect($sub->table, $list, $sub->primaryKey, $rowSub->md_subcategory_id, $rowSub->name . '_' . $rowSub->category);
                }

                $result = [
                    'header'   => $this->field->store($this->model->table, $list)
                ];

                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getCode());
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

                $docno = "TP" . $number;

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
                    $list = $this->model->getListType('md_type.isactive', 'Y', ['md_type.name', $post['search']], ['name', 'ASC'])->getResult();
                } else if (isset($post['reference']) && !empty($post['reference'])) {
                    $list = $this->model->where([
                        'isactive'              => 'Y',
                        'md_subcategory_id'     => $post['reference']
                    ])->orderBy('name', 'ASC')->findAll();
                } else {
                    $list = $this->model->getListType('md_type.isactive', 'Y', [], ['md_type.name', 'ASC'])->getResult();
                }

                if (count($list) > 0) {
                    foreach ($list as $key => $row) :
                        $response[$key]['id'] = $row->md_type_id;

                        if (isset($post['reference']) && !empty($post['reference']))
                            $response[$key]['text'] = $row->getName();
                        else
                            $response[$key]['text'] = $row->name . '_' . $row->subcategory;
                    endforeach;
                } else {
                    $response = message('error', true, 'Type is not found.');
                }
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }
}
