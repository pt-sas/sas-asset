<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Inventory;
use App\Models\M_Product;
use App\Models\M_SparePart;
use App\Models\M_Spesification;
use Config\Services;

class Spesification extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Spesification($this->request);
        $this->entity = new \App\Entities\Spesification();
    }

    public function index()
    {
        return $this->template->render('transaction/spesification/v_spesification');
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
                $ID = $value->trx_spesification_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->assetcode;
                $row[] = $value->product;
                $row[] = $value->description;
                $row[] = $value->created_by;
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

                if (!$this->validation->run($post, 'spesification')) {
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
        $mInventory = new M_Inventory($this->request);
        $mSparePart = new M_SparePart($this->request);
        $mProduct = new M_Product($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();

                $rowAssetCode = $mInventory->find($list[0]->getInventoryId());

                $list = $this->field->setDataSelect($mInventory->table, $list, $mInventory->primaryKey, $rowAssetCode->getInventoryId(), $rowAssetCode->getAssetCode());

                if (!empty($list[0]->getProcessorId())) {
                    $rowProcessor = $mSparePart->find($list[0]->getProcessorId());
                    $list = $this->field->setDataSelect($mSparePart->table, $list, 'processor_id', $rowProcessor->getSparePartId(), $rowProcessor->getName());
                }

                if (!empty($list[0]->getMotherboardId())) {
                    $rowMotherboard = $mSparePart->find($list[0]->getMotherBoardId());
                    $list = $this->field->setDataSelect($mSparePart->table, $list, 'motherboard_id', $rowMotherboard->getSparePartId(), $rowMotherboard->getName());
                }

                if (!empty($list[0]->getVideoGraphicId())) {
                    $rowVGA = $mSparePart->find($list[0]->getVideoGraphicId());
                    $list = $this->field->setDataSelect($mSparePart->table, $list, 'video_graphic_id', $rowVGA->getSparePartId(), $rowVGA->getName());
                }

                if (!empty($list[0]->getCaseId())) {
                    $rowCase = $mSparePart->find($list[0]->getCaseId());
                    $list = $this->field->setDataSelect($mSparePart->table, $list, 'case_id', $rowCase->getSparePartId(), $rowCase->getName());
                }

                if (!empty($list[0]->getPowerSupplyId())) {
                    $rowPSU = $mSparePart->find($list[0]->getPowerSupplyId());
                    $list = $this->field->setDataSelect($mSparePart->table, $list, 'power_supply_id', $rowPSU->getSparePartId(), $rowPSU->getName());
                }

                if (!empty($list[0]->getOperationId())) {
                    $rowOS = $mProduct->find($list[0]->getOperationId());
                    $list = $this->field->setDataSelect($mSparePart->table, $list, 'operation_id', $rowOS->getProductId(), $rowOS->getName());
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

    public function getList()
    {
        if ($this->request->isAjax()) {
            $post = $this->request->getVar();

            $response = [];

            try {
                if (isset($post['search'])) {
                    $list = $this->model->where('isactive', 'Y')
                        ->like('assetcode', $post['search'])
                        ->orderBy('assetcode', 'ASC')
                        ->findAll();
                } else {
                    $list = $this->model->where('isactive', 'Y')
                        ->orderBy('assetcode', 'ASC')
                        ->findAll();
                }

                foreach ($list as $key => $row) :
                    $response[$key]['id'] = $row->getAssetCode();
                    $response[$key]['text'] = $row->getAssetCode();
                endforeach;
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function getProduct()
    {
        $mInventory = new M_Inventory($this->request);
        $mProduct = new M_Product($this->request);

        if ($this->request->isAjax()) {
            $post = $this->request->getVar();

            try {
                if (isset($post['trx_inventory_id']) && $post['trx_inventory_id'] != 0) {
                    $inventory = $mInventory->find($post['trx_inventory_id']);
                    $product = $mProduct->find($inventory->getProductId());

                    $response = ['product' => $product->getName()];
                }
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }
}