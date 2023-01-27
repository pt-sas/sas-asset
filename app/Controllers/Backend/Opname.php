<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Opname;
use App\Models\M_OpnameDetail;
use App\Models\M_Status;
use App\Models\M_Supplier;
use App\Models\M_Product;
use App\Models\M_Employee;
use App\Models\M_Division;
use App\Models\M_Branch;
use App\Models\M_Depreciation;
use App\Models\M_GroupAsset;
use App\Models\M_Inventory;
use App\Models\M_Room;
use App\Models\M_Quotation;
use App\Models\M_QuotationDetail;
use Config\Services;

class Opname extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Opname($this->request);
        $this->entity = new \App\Entities\Receipt();
        $this->modelDetail = new M_OpnameDetail($this->request);
    }

    public function index()
    {
        $data = [
            'today'     => date('Y-m-d')
        ];

        return $this->template->render('transaction/opname/v_opname', $data);
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
                $ID = $value->trx_receipt_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->documentno;
                $row[] = format_dmy($value->opnamedate, '-');
                $row[] = $value->employee;
                // $row[] = $value->room;
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

                if (!$this->validation->run($post, 'opname')) {
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
        $quotation = new M_Quotation($this->request);
        $supplier = new M_Supplier($this->request);
        $employee = new M_Employee($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();
                $detail = $this->modelDetail->where($this->model->primaryKey, $id)->findAll();

                $rowEmployee = $employee->find($list[0]->getEmployeeId());
                $list = $this->field->setDataSelect($employee->table, $list, $employee->primaryKey, $rowEmployee->getEmployeeId(), $rowEmployee->getName());

                $result = [
                    'header'    => $this->field->store($this->model->table, $list),
                    'line'      => $this->tableLine($id, $detail)
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

    public function processIt()
    {
        if ($this->request->isAJAX()) {
            $post = $this->request->getVar();

            $_ID = $post['id'];
            $_DocAction = $post['docaction'];

            try {
                $row = $this->model->find($_ID);

                if (!empty($_DocAction) && $row->getDocStatus() !== $_DocAction) {
                    $line = $this->modelDetail->where($this->model->primaryKey, $_ID)->first();

                    //? Exists data line or not exist data line and docstatus not Completed
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

    public function destroyLine($id)
    {
        if ($this->request->isAJAX()) {
            try {
                $row = $this->model->getDetail($this->modelDetail->primaryKey, $id)->getRow();
                $grandTotal = ($row->grandtotal - $row->unitprice);

                //* Update table receipt
                $this->entity->setReceiptId($row->trx_receipt_id);
                $this->entity->setGrandTotal($grandTotal);

                $this->model->save($this->entity);

                //* Delete row receipt detail
                $delete = $this->modelDetail->delete($id);

                $result = $delete ? $grandTotal : false;

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
                $docNo = $this->model->getInvNumber();
                $response = message('success', true, $docNo);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function getDetailAsset()
    {
        $mInv = new M_Inventory($this->request);

        if ($this->request->isAjax()) {
            $post = $this->request->getVar();

            try {

                $detail = $mInv->getAssetLocation('trx_inventory.md_employee_id', $post['md_employee_id']);

                // if (!empty($list[0]->getSupplierId())) {
                //     $rowSupplier = $supplier->find($list[0]->getSupplierId());
                //     $list = $this->field->setDataSelect($supplier->table, $list, $supplier->primaryKey, $rowSupplier->getSupplierId(), $rowSupplier->getName());
                // }

                // if (!empty($list[0]->getEmployeeId())) {
                //     $rowEmployee = $employee->find($list[0]->getEmployeeId());
                //     $list = $this->field->setDataSelect($employee->table, $list, $employee->primaryKey, $rowEmployee->getEmployeeId(), $rowEmployee->getName());
                // }

                // $detail = $quotationDetail->where($quotation->primaryKey, $post['id'])->findAll();

                $result = [
                    'line'      => $this->tableLine(null, $detail)
                ];

                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function tableLine($set = null, $detail = [])
    {
        $table = [];

        $post = $this->request->getVar();
        //? Create
        if (empty($set) && count($detail) > 0) {
            foreach ($detail as $row) :
                $table[] = [
                    $this->field->fieldTable('input', 'text', 'assetcode', 'text-uppercase unique', null, 'readonly', null, null, $row->assetcode, 170),
                    $this->field->fieldTable('input', 'text', 'assetcode', 'text-uppercase unique', null, 'readonly', null, null, $row->product, 170),
                    $this->field->fieldTable('input', 'text', 'assetcode', 'text-uppercase unique', null, 'readonly', null, null, $row->branch, 170),
                    $this->field->fieldTable('input', 'text', 'assetcode', 'text-uppercase unique', null, 'readonly', null, null, $row->room, 170),
                    $this->field->fieldTable('input', 'text', 'assetcode', 'text-uppercase unique', null, 'readonly', null, null, $row->employee, 170),
                    $this->field->fieldTable('input', 'text', 'assetcode', 'text-uppercase unique', null, 'readonly', null, null, $row->assetcode, 170)
                ];
            endforeach;
        }

        return json_encode($table);
    }
}
