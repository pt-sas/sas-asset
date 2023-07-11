<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Sequence;
use Config\Services;

class Sequence extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
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
        if ($this->request->getMethod(true) === 'POST') {
            $table = $this->model->table;
            $select = $this->model->findAll();
            $order = $this->model->column_order;
            $sort = $this->model->order;
            $search = $this->model->column_search;

            $data = [];

            $number = $this->request->getPost('start');
            $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search);

            foreach ($list as $value) :
                $row = [];
                $ID = $value->md_sequence_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->name;
                $row[] = $value->description;
                $row[] = active($value->isautosequence);
                $row[] = $value->vformat;
                $row[] = $value->incrementno;
                $row[] = $value->maxvalue;
                $row[] = $value->currentnext;
                $row[] = $value->decimalpattern;
                // $row[] = $value->prefix;
                // $row[] = $value->suffix;
                $row[] = active($value->isgassetlevelsequence);
                $row[] = $value->gassetcolumn;
                $row[] = active($value->iscategorylevelsequence);
                $row[] = $value->categorycolumn;
                $row[] = active($value->startnewyear);
                $row[] = $value->datecolumn;
                $row[] = active($value->startnewmonth);
                $row[] = $value->startno;
                $row[] = active($value->isactive);
                $row[] = $this->template->tableButton($ID);
                $data[] = $row;
            endforeach;

            $result = [
                'draw'              => $this->request->getPost('draw'),
                'recordsTotal'      => $this->datatable->countAll($table, $select, $order, $sort, $search),
                'recordsFiltered'   => $this->datatable->countFiltered($table, $select, $order, $sort, $search),
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

                // Unset property
                $arrUnset = ['isautosequence', 'isgassetlevelsequence', 'iscategorylevelsequence', 'startnewyear', 'startnewmonth'];
                $post = $this->unsetData($post, 'N', $arrUnset);

                // Set null value
                $post['isgassetlevelsequence'] = isset($post['isgassetlevelsequence']) ?: '';

                if (!$this->validation->run($post, 'sequence')) {
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
                        ->findAll();
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

    /**
     * Function for remove property array
     *
     * @param [type] $arrOri
     * @param [type] $value
     * @param array $arr
     * @return void
     */
    public function unsetData($arrOri, $value, $arr = [])
    {
        foreach ($arr as $val) :
            if ($arrOri[$val] == $value)
                unset($arrOri[$val]);
        endforeach;

        return $arrOri;
    }
}
