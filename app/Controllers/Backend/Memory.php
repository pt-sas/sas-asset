<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Product;
use App\Models\M_Spesification;
use App\Models\M_SparePart;
use App\Models\M_SpesificationDetail;
use Config\Services;

class Memory extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Spesification($this->request);
        $this->modelDetail = new M_SpesificationDetail($this->request);
        $this->entity = new \App\Entities\SpesificationDetail();
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
                $this->entity->fill($post);

                if (!$this->validation->run($post, 'spec_detail')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    $result = $this->saveBatch("update", $this->modelDetail, null, $table);

                    if ($result) {
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
                $detail = $this->modelDetail->where([$this->model->primaryKey => $id, 'necessary' => 'MEMORY'])->findAll();

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
        $mSparePart = new M_SparePart($this->request);
        $mProduct = new M_Product($this->request);
        $mSpesification = new M_Spesification($this->request);
        $mInventory = new M_Inventory($this->request);

        $post = $this->request->getVar();


        $table = [];

        //? Create
        if (empty($set)) {
            $product = !empty($post['product']) ? $mProduct->where('name', $post['product'])->first() : null;
            $dataMemory = $mSparePart->where(['isactive' => 'Y', 'md_category_id' => 100145, 'product_category_id' => $product->getCategoryId()])->findAll();
            $table = [
                $this->field->fieldTable('select', null, 'md_sparepart_id', null, 'required', null, null, $dataMemory, null, 300, 'md_sparepart_id', 'name'),
                $this->field->fieldTable('input', 'text', 'description', null, null, null, null, null, null, 150, 'description', 'description'),
                $this->field->fieldTable('button', 'button', 'trx_spesification_detail_id')
            ];
        }

        //? Update
        if (!empty($set) && count($detail) > 0) {
            $spec = $mSpesification->where($mSpesification->primaryKey, $detail[0]->trx_spesification_id)->first();
            $inv = $mInventory->where([$mInventory->primaryKey => $spec->getInventoryId()])->first();
            $product = $mProduct->where($mProduct->primaryKey, $inv->getProductId())->first();

            $dataMemory = $mSparePart->where(['isactive' => 'Y', 'md_category_id' => 100145, 'product_category_id' => $product->getCategoryId()])->findAll();
            foreach ($detail as $row) :
                $table[] = [
                    $this->field->fieldTable('select', null, 'md_sparepart_id', null, 'required', null, null, $dataMemory, $row->md_sparepart_id, 300, 'md_sparepart_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'description', null, null, null, null, null, $row->description, 150, 'description', 'description'),
                    $this->field->fieldTable('button', 'button', 'trx_spesification_detail_id', null, null, null, null, null, $row->trx_spesification_detail_id)
                ];
            endforeach;
        }

        return json_encode($table);
    }
}
