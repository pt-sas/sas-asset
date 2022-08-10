<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Datatable;
use App\Models\M_Sequence;
use Config\Services;

class Sequence extends BaseController
{
    private $model;
    private $entity;
    protected $validation;
    protected $request;

    public function __construct()
    {
        $this->request = Services::request();
        $this->validation = Services::validation();
        $this->model = new M_Sequence($this->request);
        $this->entity = new \App\Entities\Sequence();
    }

    public function index()
    {
        $data = [
            'incrementno'   => 1,
            'currentnext'   => 1,
            'maxvalue'      => 0,
            'startno'       => 1,
        ];

        return $this->template->render('masterdata/sequence/v_sequence', $data);
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
                $ID = $value->md_sequence_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->name;
                $row[] = $value->description;
                $row[] = status($value->isautosequence);
                $row[] = $value->vformat;
                $row[] = $value->incrementno;
                $row[] = $value->maxvalue;
                $row[] = $value->currentnext;
                $row[] = $value->decimalpattern;
                // $row[] = $value->prefix;
                // $row[] = $value->suffix;
                $row[] = status($value->isgassetlevelsequence);
                $row[] = $value->gassetcolumn;
                $row[] = status($value->iscategorylevelsequence);
                $row[] = $value->categorycolumn;
                $row[] = status($value->startnewyear);
                $row[] = $value->datecolumn;
                $row[] = status($value->startnewmonth);
                $row[] = $value->startno;
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
                $this->entity->setIsAutoSequence(setCheckbox(isset($post['isautosequence'])));
                $this->entity->setIsGAssetLevelSequence(setCheckbox(isset($post['isgassetlevelsequence'])));
                $this->entity->setIsCategoryLevelSequence(setCheckbox(isset($post['iscategorylevelsequence'])));
                $this->entity->setStartNewYear(setCheckbox(isset($post['startnewyear'])));
                $this->entity->setStartNewMonth(setCheckbox(isset($post['startnewmonth'])));
                $this->entity->setCreatedBy($this->session->get('sys_user_id'));
                $this->entity->setUpdatedBy($this->session->get('sys_user_id'));

                $post['isgassetlevelsequence'] = isset($post['isgassetlevelsequence']) ?: '';

                if (!$this->validation->run($post, 'sequence')) {
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
                $this->entity->setSequenceId($post['id']);
                $this->entity->setIsActive(setCheckbox(isset($post['isactive'])));
                $this->entity->setIsAutoSequence(setCheckbox(isset($post['isautosequence'])));
                $this->entity->setIsGAssetLevelSequence(setCheckbox(isset($post['isgassetlevelsequence'])));
                $this->entity->setIsCategoryLevelSequence(setCheckbox(isset($post['iscategorylevelsequence'])));
                $this->entity->setStartNewYear(setCheckbox(isset($post['startnewyear'])));

                $post['isgassetlevelsequence'] = isset($post['isgassetlevelsequence']) ?: '';

                if (isset($post['startnewyear']))
                    $this->entity->setStartNewMonth(setCheckbox(isset($post['startnewmonth'])));
                else
                    $this->entity->setStartNewMonth('N');

                $this->entity->setUpdatedBy($this->session->get('sys_user_id'));

                if (!$this->validation->run($post, 'sequence')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    $result = $this->model->save($this->entity);

                    $msg = $result ? notification('updated') : $result;

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
                } else {
                    $list = $this->model->where('isactive', 'Y')
                        ->orderBy('name', 'ASC')
                        ->findAll(5);
                }

                foreach ($list as $key => $row) :
                    $response[$key]['id'] = $row->getSequenceId();
                    $response[$key]['text'] = $row->getName();
                endforeach;
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }
}
