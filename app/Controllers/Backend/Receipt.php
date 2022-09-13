<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
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
use Config\Services;

class Receipt extends BaseController
{
    private $model_detail;

    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Receipt($this->request);
        $this->entity = new \App\Entities\Receipt();
        $this->model_detail = new M_ReceiptDetail();
    }

    public function index()
    {
        $uri = $this->request->uri->getSegment(2);
        $status = new M_Status($this->request);

        $start_date = date('Y-m-d', strtotime('- 1 days'));
        $end_date = date('Y-m-d');

        $data = [
            'today'     => date('Y-m-d'),
            'status'    => $status->where('isactive', 'Y')
                ->like('menu_id', $uri)
                ->orderBy('name', 'ASC')
                ->findAll(),
            'default_logic' => json_decode($this->defaultLogic()),
            'date_range' => $start_date . ' - ' . $end_date
        ];

        return $this->template->render('transaction/receipt/v_receipt', $data);
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
                $row[] = format_dmy($value->receiptdate, '-');
                $row[] = !empty($value->md_supplier_id) ? $value->supplier : $value->employee;
                $row[] = $value->status;
                $row[] = $value->expenseno;
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

            //* Mandatory property for detail validation
            $post['line'] = countLine(count($table));
            $post['detail'] = [
                'table' => arrTableLine($this->mandatoryLogic($table))
            ];

            try {
                $this->entity->fill($post);
                $this->entity->setDocStatus($this->DOCSTATUS_Drafted);

                if (!$this->validation->run($post, 'receipt')) {
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
                $detail = $this->model_detail->where($this->model->primaryKey, $id)->findAll();

                $rowQuotation = $this->model->getQuotationReceipt('trx_receipt.trx_receipt_id', $id)->getRow();

                if (!empty($list[0]->getSupplierId())) {
                    $rowSupplier = $supplier->find($list[0]->getSupplierId());

                    //* Field Quotation
                    $textQuot = $rowQuotation->documentno . ' - ' . $rowQuotation->supplier . ' - ' . format_dmy($rowQuotation->quotationdate, '/') . ' - ' . $rowQuotation->grandtotal;
                    $list = $this->field->setDataSelect($quotation->table, $list, $quotation->primaryKey, $rowQuotation->trx_quotation_id, $textQuot);

                    //* Field Supplier
                    $list = $this->field->setDataSelect($supplier->table, $list, $supplier->primaryKey, $rowSupplier->getSupplierId(), $rowSupplier->getName());
                }

                if (!empty($list[0]->getEmployeeId())) {
                    $rowEmployee = $employee->find($list[0]->getEmployeeId());

                    //* Field Quotation
                    $textQuot = $rowQuotation->documentno . ' - ' . $rowQuotation->employee . ' - ' . format_dmy($rowQuotation->quotationdate, '/') . ' - ' . $rowQuotation->grandtotal;
                    $list = $this->field->setDataSelect($quotation->table, $list, $quotation->primaryKey, $rowQuotation->trx_quotation_id, $textQuot);

                    $list = $this->field->setDataSelect($employee->table, $list, $employee->primaryKey, $rowEmployee->getEmployeeId(), $rowEmployee->getName());
                }

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

            $row = $this->model->find($_ID);

            try {
                if (!empty($_DocAction) && $row->getDocStatus() !== $_DocAction) {
                    $line = $this->model_detail->where($this->model->primaryKey, $_ID)->first();

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
                $row = $this->model->getDetail($this->model_detail->primaryKey, $id)->getRow();
                $grandTotal = ($row->grandtotal - $row->unitprice);

                //* Update table receipt
                $this->entity->setReceiptId($row->trx_receipt_id);
                $this->entity->setGrandTotal($grandTotal);

                $this->model->save($this->entity);

                //* Delete row receipt detail
                $delete = $this->model_detail->delete($id);

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
        $quotation = new M_Quotation($this->request);
        $quotationDetail = new M_QuotationDetail($this->request);
        $supplier = new M_Supplier($this->request);
        $employee = new M_Employee($this->request);

        if ($this->request->isAjax()) {
            $post = $this->request->getVar();

            try {
                if (!empty($post['receipt_id'])) {
                    $checkData = $this->model_detail->where($this->model->primaryKey, $post['receipt_id'])->first();

                    //? Exists data receipt detail
                    if ($checkData)
                        $this->model_detail->where($this->model->primaryKey, $post['receipt_id'])->delete();
                }

                $list = $quotation->where($quotation->primaryKey, $post['id'])->findAll();

                if (!empty($list[0]->getSupplierId())) {
                    $rowSupplier = $supplier->find($list[0]->getSupplierId());
                    $list = $this->field->setDataSelect($supplier->table, $list, $supplier->primaryKey, $rowSupplier->getSupplierId(), $rowSupplier->getName());
                }

                if (!empty($list[0]->getEmployeeId())) {
                    $rowEmployee = $employee->find($list[0]->getEmployeeId());
                    $list = $this->field->setDataSelect($employee->table, $list, $employee->primaryKey, $rowEmployee->getEmployeeId(), $rowEmployee->getName());
                }

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

                for ($i = 1; $i <= $row->qtyentered; $i++) {
                    $table[] = [
                        $this->field->fieldTable('input', 'text', 'assetcode', 'text-uppercase unique', null, 'readonly', null, null, null, 150),
                        $this->field->fieldTable('select', null, 'product_id', null, null, 'readonly', null, $dataProduct, $row->md_product_id, 300, 'md_product_id', 'name'),
                        $this->field->fieldTable('input', 'text', 'qtyentered', 'number', null, 'readonly', null, null, 1, 50),
                        $this->field->fieldTable('input', 'text', 'unitprice', 'rupiah', 'required', $rowQuo->getIsInternalUse() === 'N' ?: 'readonly', null, null, $row->unitprice, 125),
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
            $rowReceipt = $this->model->where($this->model->primaryKey, $set)->first();

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
                    $this->field->fieldTable('input', 'text', 'unitprice', 'rupiah', 'required', $rowReceipt->getIsInternalUse() === 'N' ?: 'readonly', null, null, $row->unitprice, 125),
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

    public function mandatoryLogic($table)
    {
        $result = [];

        foreach ($table as $row) :
            // Condition to check isspare
            if ($row[4]->isspare)
                $row[5]->employee_id = 0;

            // convert format rupiah on the field unitprice
            if (isset($row[3]->unitprice))
                $row[3]->unitprice = replaceFormat($row[3]->unitprice);

            $result[] = $row;
        endforeach;

        return $result;
    }

    public function defaultLogic()
    {
        $result = [];

        //! default logic for dropdown md_status_id
        $role = $this->access->getUserRoleName($this->session->get('sys_user_id'), 'W_Not_Default_Status');

        if (!$role) {
            $result = [
                'field'         => 'md_status_id',
                'id'            => 100000, //Aset
                'condition'     => true
            ];
        }

        return json_encode($result);
    }
}
