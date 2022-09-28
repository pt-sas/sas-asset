<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Service;
use App\Models\M_ServiceDetail;
use App\Models\M_Product;
use App\Models\M_Status;
use App\Models\M_Supplier;
use App\Models\M_Inventory;

use Config\Services;

class Service extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Service($this->request);
        $this->modelDetail = new M_ServiceDetail($this->request);
        $this->entity = new \App\Entities\Service();
    }

    public function index()
    {
        $data = [
            'today'     => date('Y-m-d')
        ];

        return $this->template->render('transaction/service/v_service', $data);
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
                $ID = $value->trx_service_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->documentno;
                $row[] = format_dmy($value->servicedate, '-');
                $row[] = $value->supplier;
                $row[] = formatRupiah($value->grandtotal);
                $row[] = docStatus($value->docstatus);
                $row[] = $value->createdby;
                $row[] = $value->description;
                $row[] = $this->template->tableButton($ID, $value->docstatus);
                $data[] = $row;
            endforeach;

            $result = [
                'draw'              => $this->request->getPost('draw'),
                'recordsTotal'      => $this->datatable->countAll($table),
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

            $table = json_decode($post['table']);

            //! Mandatory property for detail validation
            $post['line'] = countLine($table);
            $post['detail'] = [
                'table' => arrTableLine($table)
            ];

            try {
                $this->entity->fill($post);
                $this->entity->setDocStatus($this->DOCSTATUS_Drafted);
                $this->entity->setGrandTotal(arrSumField('unitprice', $table));

                if (!$this->validation->run($post, 'service')) {
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
        $supplier = new M_Supplier($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();
                $detail = $this->modelDetail->where($this->model->primaryKey, $id)->findAll();

                $rowSupplier = $supplier->find($list[0]->getSupplierId());

                $list = $this->field->setDataSelect($supplier->table, $list, $supplier->primaryKey, $rowSupplier->getSupplierId(), $rowSupplier->getName());

                $result = [
                    'header'    => $this->field->store($this->model->table, $list),
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
                $result = $this->model->delete($id);
                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function destroyLine($id)
    {
        if ($this->request->isAJAX()) {
            try {
                $row = $this->model->getDetail($this->modelDetail->primaryKey, $id)->getRow();

                $grandTotal = ($row->grandtotal - $row->unitprice);

                //* Update table quotation
                $this->entity->setQuotationId($row->trx_quotation_id);
                $this->entity->setGrandTotal($grandTotal);

                $this->model->save($this->entity);

                //* Delete row quotation detail
                $delete = $this->modelDetail->delete($id);

                $result = $delete ? $grandTotal : false;

                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function processIt()
    {
        if ($this->request->isAJAX()) {
            $post = $this->request->getVar();

            $_ID = $post['id'];
            $_DocAction = $post['docaction'];

            $row = $this->model->find($_ID);

            try {
                if (!empty($_DocAction) && $row->getDocStatus() !== $_DocAction) {
                    $line = $this->modelDetail->where($this->model->primaryKey, $_ID)->first();

                    if ($line || (!$line && $_DocAction !== $this->DOCSTATUS_Completed)) {
                        $this->entity->setDocStatus($_DocAction);
                    } else if (!$line && $_DocAction === $this->DOCSTATUS_Completed) {
                        $this->entity->setDocStatus($this->DOCSTATUS_Invalid);
                    }

                    $response = $this->save();
                } else if (empty($_DocAction)) {
                    $response = message('error', true, 'Please Choose the Document Action first');
                } else {
                    $response = message('error', true, 'Please reload the Document');
                }
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function tableLine($set = null, $detail = [])
    {
        $uri = $this->request->uri->getSegment(2);
        $inventory = new M_Inventory($this->request);
        $product = new M_Product($this->request);
        $status = new M_Status($this->request);

        $dataInventory = $inventory->where('isactive', 'Y')
            ->orderBy('assetcode', 'ASC')
            ->findAll();
        $dataProduct = $product->where('isactive', 'Y')->findAll();
        $dataStatus = $status->where([
            'isactive'  => 'Y',
            'isline'    => 'Y'
        ])->like('menu_id', $uri, 'both')
            ->orderBy('name', 'ASC')
            ->findAll();

        $table = [];

        //? Create
        if (empty($set)) {
            $table = [
                $this->field->fieldTable('select', null, 'assetcode', 'unique', 'required', null, null, $dataInventory, null, 170, 'assetcode', 'assetcode'),
                $this->field->fieldTable('select', null, 'md_product_id', null, 'required', 'readonly', null, $dataProduct, null, 300, 'md_product_id', 'name'),
                $this->field->fieldTable('input', 'text', 'unitprice', 'rupiah', 'required', null, null, null, null, 125),
                $this->field->fieldTable('select', null, 'md_status_id', null, 'required', null, null, $dataStatus, 'On Service', 150, 'md_status_id', 'name'),
                $this->field->fieldTable('input', 'text', 'description', null, null, null, null, null, null, 250),
                $this->field->fieldTable('button', 'button', 'trx_service_detail_id')
            ];
        }

        //? Update
        if (!empty($set) && count($detail) > 0) {
            foreach ($detail as $row) :
                $table[] = [
                    $this->field->fieldTable('select', null, 'assetcode', 'unique', 'required', null, null, $dataInventory, $row->assetcode, 170, 'assetcode', 'assetcode'),
                    $this->field->fieldTable('select', null, 'md_product_id', null, 'required', 'readonly', null, $dataProduct, $row->md_product_id, 300, 'md_product_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'unitprice', 'rupiah', null, null, null, null, $row->unitprice, 125),
                    $this->field->fieldTable('select', null, 'md_status_id', null, 'required', null, null, $dataStatus, $row->md_status_id, 150, 'md_status_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'description', null, null, null, null, null, $row->description, 250),
                    $this->field->fieldTable('button', 'button', 'trx_service_detail_id', null, null, null, null, null, $row->trx_service_detail_id)
                ];
            endforeach;
        }

        return json_encode($table);
    }

    public function getSeqCode()
    {
        if ($this->request->isAJAX()) {
            try {
                $docNo = $this->model->getInvNumber();
                $response = message('success', true, $docNo);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }
}
