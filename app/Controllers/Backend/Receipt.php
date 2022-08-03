<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Datatable;
use App\Models\M_Receipt;
use App\Models\M_ReceiptDetail;
use App\Models\M_Status;
use App\Models\M_Supplier;
use App\Models\M_Product;
use App\Models\M_Employee;
use App\Models\M_Division;
use App\Models\M_Branch;
use App\Models\M_Room;
use App\Models\M_Quotation;
use App\Models\M_QuotationDetail;
use App\Models\M_Inventory;
use App\Models\M_Transaction;
use Config\Services;

class Receipt extends BaseController
{
    private $model;
    private $entity;
    protected $validation;
    protected $request;

    public function __construct()
    {
        $this->request = Services::request();
        $this->validation = Services::validation();
        $this->model = new M_Receipt($this->request);
        $this->entity = new \App\Entities\Receipt();
    }

    public function index()
    {
        $uri = $this->request->uri->getSegment(2);
        $status = new M_Status($this->request);
        $supplier = new M_Supplier($this->request);

        $data = [
            'today'     => date('Y-m-d'),
            'status'    => $status->where('isactive', 'Y')
                ->like('menu_id', $uri)
                ->orderBy('name', 'ASC')
                ->findAll(),
            'supplier'  => $supplier->where('isactive', 'Y')
                ->orderBy('name', 'ASC')
                ->findAll()
        ];

        return $this->template->render('transaction/receipt/v_receipt', $data);
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
                $ID = $value->trx_receipt_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->documentno;
                $row[] = format_dmy($value->receiptdate, '-');
                $row[] = $value->supplier;
                $row[] = $value->status;
                $row[] = $value->invoiceno;
                $row[] = formatRupiah($value->grandtotal);
                $row[] = docStatus($value->docstatus);
                $row[] = $value->createdby;
                $row[] = $value->description;
                $row[] = $this->template->tableButton($ID, $value->docstatus);
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
        if ($this->request->getMethod(true) === 'POST') {
            $post = $this->request->getVar();

            $table = json_decode($post['table']);

            //* Mandatory property for detail validation
            $post['line'] = countLine(count($table));
            $post['detail'] = [
                'table' => arrTableLine($this->model->mandatoryLogic($table))
            ];

            try {
                $this->entity->fill($post);
                $this->entity->setDocStatus($this->DOCSTATUS_Drafted);
                $this->entity->setCreatedBy($this->session->get('sys_user_id'));
                $this->entity->setUpdatedBy($this->session->get('sys_user_id'));

                if (!$this->validation->run($post, 'receipt')) {
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
        $receiptDetail = new M_ReceiptDetail();
        $quotation = new M_Quotation($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();
                $detail = $receiptDetail->where($this->model->primaryKey, $id)->findAll();

                $rowQuotation = $quotation->checkExistQuotation($id)->getRow();
                $textQuot = $rowQuotation->documentno . ' - ' . $rowQuotation->supplier . ' - ' . format_dmy($rowQuotation->quotationdate, '/') . ' - ' . $rowQuotation->grandtotal;

                $list = $this->field->setDataSelect($quotation->table, $list, $quotation->primaryKey, $rowQuotation->trx_quotation_id, $textQuot);

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

    public function edit()
    {
        if ($this->request->getMethod(true) === 'POST') {
            $post = $this->request->getVar();

            $table = json_decode($post['table']);

            //* Mandatory property for detail validation
            $post['line'] = countLine(count($table));
            $post['detail'] = [
                'table' => arrTableLine($this->model->mandatoryLogic($table))
            ];

            //? Set value trx_quotation_id null
            if (!isset($post['trx_quotation_id']))
                $post['trx_quotation_id'] = "";

            try {
                $this->entity->fill($post);
                $this->entity->setReceiptId($post['id']);
                $this->entity->setUpdatedBy($this->session->get('sys_user_id'));

                if (!$this->validation->run($post, 'receipt')) {
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

    public function processIt()
    {
        $receiptDetail = new M_ReceiptDetail();
        $quotationDetail = new M_QuotationDetail();
        $inventory = new M_Inventory($this->request);
        $transaction = new M_Transaction();

        if ($this->request->isAJAX()) {
            $_ID = $this->request->getGet('id');
            $_DocAction = $this->request->getGet('docaction');

            $row = $this->model->find($_ID);

            $msg = true;

            try {
                if (!empty($_DocAction) && $row->getDocStatus() !== $_DocAction) {
                    $this->entity->setReceiptId($_ID);
                    $this->entity->setUpdatedBy($this->session->get('sys_user_id'));

                    $line = $receiptDetail->where($this->model->primaryKey, $_ID)->findAll();

                    //? Exists data line or not exist data line and docstatus not Completed
                    if (count($line) > 0 || (count($line) == 0 && $_DocAction !== $this->DOCSTATUS_Completed)) {
                        $this->entity->setDocStatus($_DocAction);
                    } else if (count($line) == 0 && $_DocAction === $this->DOCSTATUS_Completed) {
                        $this->entity->setDocStatus($this->DOCSTATUS_Invalid);
                        $msg = 'Document cannot be processed';
                    }

                    $result = $this->model->save($this->entity);

                    //? Exists data line and docstatus Completed
                    if (count($line) > 0 && $_DocAction === $this->DOCSTATUS_Completed) {
                        //* Update qtyreceipt table trx_quotation_detail
                        $arrQuoDetail = $receiptDetail->getSumQtyGroup($_ID)->getResult();
                        $quotationDetail->updateQty($arrQuoDetail, 'qtyreceipt');

                        //* Passing data to table inventory
                        $line = $this->field->mergeArrObject($line, [
                            'md_status_id'      => $row->getStatusId()
                        ]);

                        $inventory->create($line);

                        //* Passing data to table transaction
                        $line = $this->field->mergeArrObject($line, [
                            'transactiontype'   => $this->Inventory_In,
                            'transactiondate'   => $row->getReceiptDate()
                        ]);

                        $transaction->create($line);
                    }

                    $msg = $result ? $msg : $result;

                    $response = message('success', true, $msg);
                } else if (empty($_DocAction)) {
                    $response = message('error', true, 'Please Choose the Document Action first');
                } else {
                    $response = message('error', true, 'Please reload the Document');
                }
            } catch (\Exception $e) {
                $response = message('error', false, $e->getTrace());
            }

            return $this->response->setJSON($response);
        }
    }

    public function destroyLine($id)
    {
        $receiptDetail = new M_ReceiptDetail();

        if ($this->request->isAJAX()) {
            try {
                $row = $this->model->getDetail($receiptDetail->primaryKey, $id)->getRow();
                $grandTotal = ($row->grandtotal - $row->unitprice);

                //* Update table receipt
                $this->entity->setReceiptId($row->trx_receipt_id);
                $this->entity->setGrandTotal($grandTotal);

                $this->model->save($this->entity);

                //* Delete row receipt detail
                $delete = $receiptDetail->delete($id);

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

    public function getDetailQuotation()
    {
        $receiptDetail = new M_ReceiptDetail();
        $quotation = new M_Quotation($this->request);
        $quotationDetail = new M_QuotationDetail();

        if ($this->request->isAjax()) {
            $post = $this->request->getVar();

            try {
                if (!empty($post['receipt_id'])) {
                    $checkData = $receiptDetail->where($this->model->primaryKey, $post['receipt_id'])->first();

                    //? Exists data receipt detail
                    if ($checkData)
                        $receiptDetail->where($this->model->primaryKey, $post['receipt_id'])->delete();
                }

                $list = $quotation->where($quotation->primaryKey, $post['id'])->findAll();
                $detail = $quotationDetail->where($quotation->primaryKey, $post['id'])->findAll();

                $result = [
                    'header'    => $this->field->store('trx_quotation', $list),
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
        $product = new M_Product($this->request);
        $employee = new M_Employee($this->request);
        $division = new M_Division($this->request);
        $branch = new M_Branch($this->request);
        $room = new M_Room($this->request);
        $quotation = new M_Quotation($this->request);
        $supplier = new M_Supplier($this->request);

        /**
         * RM00039 => IT
         * RM00040 => RUANG IT - BARANG BAGUS
         * RM00041 => RUANG IT - BARANG RUSAK
         */
        $ROOM_IT = ['RM00039', 'RM00040'];

        $table = [];

        //* Master data
        $dataProduct = $product->where('isactive', 'Y')
            ->orderBy('name', 'ASC')
            ->findAll();
        $dataEmployee = $employee->where('isactive', 'Y')
            ->orderBy('name', 'ASC')
            ->findAll();
        $dataDivision = $division->where('isactive', 'Y')
            ->orderBy('name', 'ASC')
            ->findAll();
        $dataBranch = $branch->where('isactive', 'Y')
            ->orderBy('name', 'ASC')
            ->findAll();

        //? Create
        if (empty($set) && count($detail) > 0) {
            foreach ($detail as $row) :
                $rowQuo = $quotation->where($quotation->primaryKey, $row->trx_quotation_id)->first();
                $rowSupp = $supplier->find($rowQuo->getSupplierId());

                for ($i = 1; $i <= $row->qtyentered; $i++) {
                    $table[] = [
                        $this->field->fieldTable('input', 'text', 'assetcode', 'text-uppercase unique', null, 'readonly', null, null, null, 150),
                        $this->field->fieldTable('select', null, 'product_id', null, null, 'readonly', null, $dataProduct, $row->md_product_id, 300, 'md_product_id', 'name'),
                        $this->field->fieldTable('input', 'text', 'qtyentered', 'number', null, 'readonly', null, null, 1, 50),
                        $this->field->fieldTable('input', 'text', 'unitprice', 'rupiah', 'required', $rowSupp->getName() === 'SAS' ? 'readonly' : null, null, null, $row->unitprice, 125),
                        $this->field->fieldTable('input', 'checkbox', 'isspare', null, null, null, null, null, $row->isspare),
                        $this->field->fieldTable('select', 'text', 'employee_id', null, $row->isspare == 'Y' ?: 'required', $row->isspare == 'Y' ?? 'readonly', null, $dataEmployee, $row->md_employee_id, 200, 'md_employee_id', 'name'),
                        $this->field->fieldTable('select', 'text', 'branch_id', null, $row->isspare == 'Y' ?: 'required', $row->isspare == 'Y' ?? 'readonly', null, null, null, 200),
                        $this->field->fieldTable('select', 'text', 'division_id', null, $row->isspare == 'Y' ?: 'required', $row->isspare == 'Y' ?? 'readonly', null, null, null, 200),
                        $this->field->fieldTable('select', 'text', 'room_id', null, 'required', null, null, null, null, 250),
                        $this->field->fieldTable('input', 'text', 'desc', null, null, null, null, null, $row->description, 250),
                        $this->field->fieldTable('button', 'button', 'delete', null, null, null, null, null, $row->trx_quotation_detail_id, 0, 'value') // Manipulate Set id on the attribute value
                    ];
                }
            endforeach;
        }

        //? Update
        if (!empty($set) && count($detail) > 0) {
            $receipt = $this->model->where($this->model->primaryKey, $set)->first();
            $rowSupp = $supplier->find($receipt->getSupplierId());

            foreach ($detail as $row) :
                if ($row->isspare == 'Y')
                    $dataRoom = $room->where('isactive', 'Y')
                        ->whereIn('value', $ROOM_IT)
                        ->orderBy('name', 'ASC')
                        ->findAll();
                else
                    $dataRoom = $room->where('isactive', 'Y')
                        ->orderBy('name', 'ASC')
                        ->findAll();

                $table[] = [
                    $this->field->fieldTable('input', 'text', 'assetcode', 'text-uppercase unique', null, 'readonly', null, null, $row->assetcode, 150),
                    $this->field->fieldTable('select', null, 'product_id', null, null, 'readonly', null, $dataProduct, $row->md_product_id, 300, 'md_product_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'qtyentered', 'number', null, 'readonly', null, null, 1, 50),
                    $this->field->fieldTable('input', 'text', 'unitprice', 'rupiah', 'required', $rowSupp->getName() === 'SAS' ? 'readonly' : null, null, null, $row->unitprice, 125),
                    $this->field->fieldTable('input', 'checkbox', 'isspare', null, null, null, null, null, $row->isspare),
                    $this->field->fieldTable('select', 'text', 'employee_id', null, $row->isspare == 'Y' ?: 'required', $row->isspare == 'Y' ?? 'readonly', null, $dataEmployee, $row->md_employee_id, 200, 'md_employee_id', 'name'),
                    $this->field->fieldTable('select', 'text', 'branch_id', null, $row->isspare == 'Y' ?: 'required', $row->isspare == 'Y' ?? 'readonly', null, $dataBranch, $row->md_branch_id, 200, 'md_branch_id', 'name'),
                    $this->field->fieldTable('select', 'text', 'division_id', null, $row->isspare == 'Y' ?: 'required', $row->isspare == 'Y' ?? 'readonly', null, $dataDivision, $row->md_division_id, 200, 'md_division_id', 'name'),
                    $this->field->fieldTable('select', 'text', 'room_id', null, 'required', null, null, $dataRoom, $row->md_room_id, 250, 'md_room_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'desc', null, null, null, null, null, $row->description, 250),
                    $this->field->fieldTable('button', 'button', 'delete', null, null, null, null, null, $row->trx_receipt_detail_id)
                ];
            endforeach;
        }

        return json_encode($table);
    }
}
