<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Quotation;
use App\Models\M_QuotationDetail;
use App\Models\M_Status;
use App\Models\M_Supplier;
use App\Models\M_Product;
use App\Models\M_Employee;
use Config\Services;

class Quotation extends BaseController
{
    private $model;
    private $model_detail;
    private $entity;

    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Quotation($this->request);
        $this->model_detail = new M_QuotationDetail($this->request);
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
            'default_logic' => json_decode($this->defaultLogic())
        ];

        return $this->template->render('transaction/quotation/v_quotation', $data);
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
            $where = [
                'trx_quotation.isinternaluse' => 'N'
            ];

            $data = [];

            $number = $this->request->getPost('start');
            $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search, $join, $where);

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
                'recordsTotal'      => $this->datatable->countAll($table),
                'recordsFiltered'   => $this->datatable->countFiltered($table, $select, $order, $sort, $search, $join, $where),
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
                'table' => arrTableLine($this->mandatoryLogic($table))
            ];

            try {
                $this->entity->fill($post);
                $this->entity->setDocStatus($this->DOCSTATUS_Drafted);
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
        $supplier = new M_Supplier($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();
                $detail = $this->model_detail->where($this->model->primaryKey, $id)->findAll();

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
                'table' => arrTableLine($this->mandatoryLogic($table))
            ];

            try {
                $this->entity->fill($post);
                $this->entity->setQuotationId($post['id']);
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
        if ($this->request->isAJAX()) {
            $post = $this->request->getVar();

            $_ID = $post['id'];
            $_DocAction = $post['docaction'];

            $row = $this->model->find($_ID);

            $msg = true;

            try {
                if (!empty($_DocAction) && $row->getDocStatus() !== $_DocAction) {
                    $line = $this->model_detail->where($this->model->primaryKey, $_ID)->first();

                    if ($line || (!$line && $_DocAction !== $this->DOCSTATUS_Completed)) {
                        $this->entity->setDocStatus($_DocAction);
                    } else if (!$line && $_DocAction === $this->DOCSTATUS_Completed) {
                        $this->entity->setDocStatus($this->DOCSTATUS_Invalid);
                        $msg = 'Document cannot be processed';
                    }

                    $this->entity->setQuotationId($_ID);
                    $this->entity->setUpdatedBy($this->session->get('sys_user_id'));

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
        if ($this->request->isAJAX()) {
            try {
                $row = $this->model->getDetail($this->model_detail->primaryKey, $id)->getRow();

                $grandTotal = ($row->grandtotal - $row->lineamt);

                //* Update table quotation
                $this->entity->setQuotationId($row->trx_quotation_id);
                $this->entity->setGrandTotal($grandTotal);

                $this->model->save($this->entity);

                //* Delete row quotation detail
                $delete = $this->model_detail->delete($id);

                $result = $delete ? $grandTotal : false;

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

        $dataEmployee = $employee->where('isactive', 'Y')
            ->orderBy('name', 'ASC')
            ->findAll();

        $table = [];

        //? Create
        $data = $this->request->getPost('data');
        $arrData = json_decode($data);

        if ($set === 'create' && count($arrData) > 0) {
            foreach ($arrData as $row) :
                $valPro = $product->find($row[0]->product_id);

                $lineamt = 0;
                if (!empty($row[1]->qtyentered) && !empty($row[2]->unitprice))
                    $lineamt = $row[1]->qtyentered * replaceFormat($row[2]->unitprice);

                $table[] = [
                    $this->field->fieldTable('input', 'text', 'product_id', 'text-uppercase', 'required', 'readonly', null, null, $valPro->getName(), 300),
                    $this->field->fieldTable('input', 'text', 'qtyentered', 'number', 'required', null, null, null, $row[1]->qtyentered, 70),
                    $this->field->fieldTable('input', 'text', 'unitprice', 'rupiah', 'required', null, null, null, replaceFormat($row[2]->unitprice), 125),
                    $this->field->fieldTable('input', 'text', 'lineamt', 'rupiah', 'required', 'readonly', null, null, $lineamt, 125),
                    $this->field->fieldTable('input', 'checkbox', 'isspare', null, null, null, $row[3]->isspare ? 'checked' : null),
                    $this->field->fieldTable('select', null, 'employee_id', null, 'required', $row[3]->isspare ?? 'readonly', null, $dataEmployee, !empty($row[4]->employee_id) ? $row[4]->employee_id : null, 200, 'md_employee_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'spek', null, null, null, null, null, $row[5]->spek, 250),
                    $this->field->fieldTable('input', 'text', 'desc', null, null, null, null, null, $row[6]->desc, 250),
                    $this->field->fieldTable('button', 'button', 'delete')
                ];
            endforeach;
        }

        //? Update
        if (!empty($set) && count($detail) > 0) {
            foreach ($detail as $row) :
                $valPro = $product->find($row->md_product_id);

                $table[] = [
                    $this->field->fieldTable('input', 'text', 'product_id', 'text-uppercase', 'required', 'readonly', null, null, $valPro->getName(), 300),
                    $this->field->fieldTable('input', 'text', 'qtyentered', 'number', 'required', null, null, null, $row->qtyentered, 70),
                    $this->field->fieldTable('input', 'text', 'unitprice', 'rupiah', 'required', null, null, null, $row->unitprice, 125),
                    $this->field->fieldTable('input', 'text', 'lineamt', 'rupiah', 'required', 'readonly', null, null, $row->lineamt, 125),
                    $this->field->fieldTable('input', 'checkbox', 'isspare', null, null, null, null, null, $row->isspare),
                    $this->field->fieldTable('select', 'text', 'employee_id', null, 'required', $row->isspare == 'Y' ?? 'readonly', null, $dataEmployee, $row->md_employee_id, 200, 'md_employee_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'spek', null, null, null, null, null, $row->specification, 250),
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
                $docNo = $this->model->getInvNumber('isinternaluse', 'N');
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
                } else if (isset($post['trx_receipt_id'])) {
                    $list = $this->model->checkExistQuotation($post['trx_receipt_id'])->getResult();
                } else {
                    $list = $this->model->checkExistQuotation()->getResult();
                }

                foreach ($list as $key => $row) :
                    $bp = $row->supplier;

                    if (!empty($row->employee))
                        $bp = $row->employee;

                    $response[$key]['id'] = $row->trx_quotation_id;
                    $response[$key]['text'] = $row->documentno . ' - ' . $bp . ' - ' . format_dmy($row->quotationdate, '/') . ' - ' . $row->grandtotal;
                endforeach;
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function mandatoryLogic($table)
    {
        $result = [];

        foreach ($table as $row) :

            // Condition to check isspare
            if ($row[4]->isspare)
                $row[5]->employee_id = 0;

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
