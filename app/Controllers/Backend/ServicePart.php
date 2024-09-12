<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_ServicePart;
use App\Models\M_Product;
use App\Models\M_ServiceDetail;
use App\Models\M_SparePart;

use Config\Services;

class ServicePart extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_ServiceDetail($this->request);
        $this->modelDetail = new M_ServicePart($this->request);
        $this->entity = new \App\Entities\ServicePart();
    }

    public function create()
    {
        if ($this->request->getMethod(true) === 'POST') {
            $post = $this->request->getVar();

            $table = json_decode($post['table']);

            //! Mandatory property for detail validation
            $post['line'] = countLine($table);
            $post['detail'] = [
                'table' => arrTableLine($table)
            ];

            try {
                if (!$this->validation->run($post, 'serviceParts')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    $result = $this->saveBatch("update", $this->modelDetail, null, $table);

                    if ($result) {
                        $entity = new \App\Entities\ServiceDetail();
                        $entity->lineamt = arrSumField('lineamt', $table);
                        $entity->{$this->model->primaryKey} = $post['id'];
                        $this->model->save($entity);

                        $response = message('success', true, 'Your data has been successfully saved !');
                    } else {
                        $response = message('error', false, 'No data to Insert');
                    }
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
                $detail = $this->modelDetail->where($this->model->primaryKey, $id)->findAll();

                $result = [
                    'line'      => $this->tableLine('edit', $detail)
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
                $result = $this->modelDetail->delete($id);
                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function tableLine($set = null, $detail = [])
    {
        $mProduct = new M_Product($this->request);
        $mSparePart = new M_SparePart($this->request);

        $post = $this->request->getVar();

        $table = [];

        //? Create
        if (empty($set)) {
            $rowProduct = $mProduct->where([
                'isactive'      => 'Y',
                'name'          => $post['product']
            ])->first();

            $dataPart = $mSparePart->where([
                'isactive'          => 'Y',
                'md_product_id'     => $rowProduct->getProductId()
            ])->orderBy('name', 'ASC')
                ->findAll();

            $table = [
                $this->field->fieldTable('select', null, 'md_sparepart_id', 'unique', 'required', null, null, $dataPart, null, 250, 'md_sparepart_id', 'name'),
                $this->field->fieldTable('input', 'text', 'qtyentered', 'number', null, null, null, null, null, 100),
                $this->field->fieldTable('input', 'text', 'unitprice', 'rupiah', null, null, null, null, null, 200),
                $this->field->fieldTable('input', 'text', 'lineamt', 'rupiah', null, 'readonly', null, null, null, 200),
                $this->field->fieldTable('button', 'button', 'trx_service_part_id')
            ];
        }

        //? Update
        if (!empty($set) && count($detail) > 0) {
            $serviceDetail = $this->model->find($detail[0]->trx_service_detail_id);

            $dataPart = $mSparePart->where([
                'isactive'          => 'Y',
                'md_product_id'     => $serviceDetail->md_product_id
            ])->orderBy('name', 'ASC')
                ->findAll();

            foreach ($detail as $row) :
                $table[] = [
                    $this->field->fieldTable('select', null, 'md_sparepart_id', 'unique', 'required', null, null, $dataPart, $row->md_sparepart_id, 250, 'md_sparepart_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'qtyentered', 'number', null, null, null, null, $row->qtyentered, 100),
                    $this->field->fieldTable('input', 'text', 'unitprice', 'rupiah', null, null, null, null, $row->unitprice, 200),
                    $this->field->fieldTable('input', 'text', 'lineamt', 'rupiah', null, 'readonly', null, null, $row->lineamt, 200),
                    $this->field->fieldTable('button', 'button', 'trx_service_part_id', null, null, null, null, null, $row->trx_service_part_id)
                ];
            endforeach;
        }

        return json_encode($table);
    }
}
