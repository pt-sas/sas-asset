<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Datatable;
use App\Models\M_Quotation;
use App\Models\M_QuotationDetail;
use App\Models\M_Status;
use App\Models\M_Supplier;
use App\Models\M_Product;
use App\Models\M_Employee;
use App\Models\M_Receipt;
use Config\Services;

class Quotation extends BaseController
{
    private $model;
    private $entity;
    protected $validation;
    protected $request;

    public function __construct()
    {
        $this->request = Services::request();
        $this->validation = Services::validation();
        $this->model = new M_Quotation($this->request);
        $this->entity = new \App\Entities\Quotation();
    }

    public function index()
    {
        $uri = $this->request->uri->getSegment(2);
        $status = new M_Status($this->request);

        $data = [
            'today'     => date('Y-m-d'),
            'status'    => $status->where('isactive', 'Y')
                ->like('menu_id', $uri)
                ->orderBy('name', 'ASC')
                ->findAll(),
        ];

        return $this->template->render('transaction/quotation/v_quotation', $data);
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
                $ID = $value->trx_quotation_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->documentno;
                $row[] = format_dmy($value->quotationdate, '-');
                $row[] = $value->supplier;
                $row[] = $value->status;
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

            // Mandatory property for detail validation
            $post['line'] = countLine(count($table));
            $post['detail'] = [
                'table' => arrTableLine($this->model->mandatoryLogic($table))
            ];

            try {
                $this->entity->fill($post);
                $this->entity->setDocStatus($this->DOCSTATUS_Drafted);
                $this->entity->setIsInternalUse(setCheckbox(isset($post['isinternaluse'])));
                $this->entity->setCreatedBy($this->session->get('sys_user_id'));
                $this->entity->setUpdatedBy($this->session->get('sys_user_id'));

                if (!$this->validation->run($post, 'quotation')) {
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
        $quotationDetail = new M_QuotationDetail();
        $supplier = new M_Supplier($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();
                $detail = $quotationDetail->where($this->model->primaryKey, $id)->findAll();

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

    public function edit()
    {
        if ($this->request->isAJAX()) {
            $post = $this->request->getVar();

            $table = json_decode($post['table']);

            // Mandatory property for detail validation
            $post['line'] = countLine(count($table));
            $post['detail'] = [
                'table' => arrTableLine($this->model->mandatoryLogic($table))
            ];

            try {
                $this->entity->fill($post);
                $this->entity->setQuotationId($post['id']);
                $this->entity->setIsInternalUse(setCheckbox(isset($post['isinternaluse'])));
                $this->entity->setUpdatedBy($this->session->get('sys_user_id'));

                if (!$this->validation->run($post, 'quotation')) {
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
        $quotationDetail = new M_QuotationDetail();

        if ($this->request->isAJAX()) {
            $_ID = $this->request->getGet('id');
            $_DocAction = $this->request->getGet('docaction');

            $row = $this->model->find($_ID);

            $msg = true;

            try {
                if (!empty($_DocAction) && $row->getDocStatus() !== $_DocAction) {
                    $this->entity->setQuotationId($_ID);
                    $this->entity->setUpdatedBy($this->session->get('sys_user_id'));

                    $line = $quotationDetail->where($this->model->primaryKey, $_ID)->first();

                    if (!empty($line) || (empty($line) && $_DocAction !== $this->DOCSTATUS_Completed)) {
                        $this->entity->setDocStatus($_DocAction);
                    } else if (empty($line) && $_DocAction === $this->DOCSTATUS_Completed) {
                        $this->entity->setDocStatus($this->DOCSTATUS_Invalid);
                        $msg = 'Document cannot be processed';
                    }

                    $result = $this->model->save($this->entity);

                    $msg = $result ? $msg : $result;

                    $response = message('success', true, $msg);
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
        $quotationDetail = new M_QuotationDetail();

        if ($this->request->isAJAX()) {
            try {
                $row = $this->model->getDetail($quotationDetail->primaryKey, $id)->getRow();

                $grandTotal = ($row->grandtotal - $row->lineamt);

                //* Update table quotation
                $this->entity->setQuotationId($row->trx_quotation_id);
                $this->entity->setGrandTotal($grandTotal);

                $this->model->save($this->entity);

                //* Delete row quotation detail
                $delete = $quotationDetail->delete($id);

                $result = $delete ? $grandTotal : false;

                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getTrace());
            }

            return $this->response->setJSON($response);
        }
    }

    public function tableLine($set = null, $detail = [])
    {
        $product = new M_Product($this->request);
        $employee = new M_Employee($this->request);

        $post = $this->request->getVar();

        $dataProduct = $product->where('isactive', 'Y')
            ->orderBy('name', 'ASC')
            ->findAll();
        $dataEmployee = $employee->where('isactive', 'Y')
            ->orderBy('name', 'ASC')
            ->findAll();

        $table = [];

        //? Create
        if (empty($set)) {
            $table = [
                $this->field->fieldTable('select', null, 'product_id', null, 'required', null, null, $dataProduct, null, 300, 'md_product_id', 'name'),
                $this->field->fieldTable('input', 'text', 'qtyentered', 'number', 'required', null, null, null, null, 70),
                $this->field->fieldTable('input', 'text', 'unitprice', 'rupiah', 'required', isset($post['isinternaluse']) ? 'readonly' : null, null, null, isset($post['isinternaluse']) ? 0 : null, 125),
                $this->field->fieldTable('input', 'text', 'lineamt', 'rupiah', 'required', 'readonly', null, null, 0, 125),
                $this->field->fieldTable('input', 'checkbox', 'isspare', null, null, null, 'checked'),
                $this->field->fieldTable('select', null, 'employee_id', null, 'required', 'readonly', null, $dataEmployee, 'IT', 200, 'md_employee_id', 'name'),
                $this->field->fieldTable('input', 'text', 'spek', null, null, null, null, null, null, 250),
                $this->field->fieldTable('input', 'text', 'desc', null, null, null, null, null, null, 250),
                $this->field->fieldTable('button', 'button', 'delete')
            ];
        }

        //? Update
        if (!empty($set) && count($detail) > 0) {
            foreach ($detail as $row) :
                $quotation = $this->model->where($this->model->primaryKey, $row->trx_quotation_id)->first();

                $table[] = [
                    $this->field->fieldTable('select', null, 'product_id', null, 'required', null, null, $dataProduct, $row->md_product_id, 300, 'md_product_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'qtyentered', 'rupiah', 'required', null, null, null, $row->qtyentered, 70),
                    $this->field->fieldTable('input', 'text', 'unitprice', 'rupiah', 'required', $quotation->isinternaluse == 'Y' ? 'readonly' : null, null, null, $row->unitprice, 125),
                    $this->field->fieldTable('input', 'text', 'lineamt', 'rupiah', 'required', 'readonly', null, null, $row->lineamt, 125),
                    $this->field->fieldTable('input', 'checkbox', 'isspare', null, null, null, null, null, $row->isspare),
                    $this->field->fieldTable('select', 'text', 'employee_id', null, 'required', $row->isspare == 'Y' ?? 'readonly', null, $dataEmployee, $row->md_employee_id, 200, 'md_employee_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'spek', null, null, null, null, null, $row->spesification, 250),
                    $this->field->fieldTable('input', 'text', 'desc', null, null, null, null, null, $row->description, 250),
                    $this->field->fieldTable('button', 'button', 'delete', null, null, null, null, null, $row->trx_quotation_detail_id)
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

    public function getList()
    {
        if ($this->request->isAjax()) {
            $post = $this->request->getVar();

            $response = [];

            try {
                if (isset($post['search'])) {
                    $list = $this->model->checkExistQuotation(null, [
                        'q.documentno'  => $post['search']
                    ])->getResult();
                } else {
                    $list = $this->model->checkExistQuotation()->getResult();
                }

                foreach ($list as $key => $row) :
                    $response[$key]['id'] = $row->trx_quotation_id;
                    $response[$key]['text'] = $row->documentno . ' - ' . $row->supplier . ' - ' . format_dmy($row->quotationdate, '/') . ' - ' . $row->grandtotal;
                endforeach;
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }
}
