<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Quotation;
use App\Models\M_QuotationDetail;
use App\Models\M_Status;
use App\Models\M_Product;
use App\Models\M_Employee;
use App\Models\M_Reference;
use App\Models\M_Supplier;
use App\Models\M_Receipt;
use Config\Services;

class Internal extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Quotation($this->request);
        $this->modelDetail = new M_QuotationDetail($this->request);
        $this->entity = new \App\Entities\Quotation();
    }

    public function index()
    {
        $uri = $this->request->uri->getSegment(2);
        $status = new M_Status($this->request);
        $reference = new M_Reference($this->request);

        $start_date = date('Y-m-d', strtotime('- 1 days'));
        $end_date = date('Y-m-d');

        $data = [
            'today'     => date('Y-m-d'),
            'status'    => $status->where('isactive', 'Y')
                ->like('menu_id', $uri)
                ->orderBy('name', 'ASC')
                ->findAll(),
            'default_logic' => json_decode($this->defaultLogic()),
            'date_range' => $start_date . ' - ' . $end_date,
            'ref_list' => $reference->findBy([
                'sys_reference.name'              => 'FromList',
                'sys_reference.isactive'          => 'Y',
                'sys_ref_detail.isactive'         => 'Y',
            ], null, [
                'field'     => 'sys_ref_detail.sys_ref_detail_id',
                'option'    => 'ASC'
            ])->getResult()
        ];

        return $this->template->render('transaction/internaluse/v_internaluse', $data);
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
            $where['trx_quotation.isinternaluse'] = 'Y';

            //? Check is use exist role W_Not_Default_Status 
            if (!$this->access->getUserRoleName($this->session->get('sys_user_id'), 'W_Not_Default_Status'))
                $where['trx_quotation.md_status_id'] = 100000;

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
                $row[] = !empty($value->employee) ? $value->employee : $value->supplier;
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
                'recordsTotal'      => $this->datatable->countAll($table, $select, $order, $sort, $search, $join, $where),
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

            //? Set null data for depreciation type combobox not choose
            if (!isset($post['isfrom']))
                $post['isfrom'] = "";

            //! Mandatory property for detail validation
            $post['line'] = countLine($table);
            $post['detail'] = [
                'table' => arrTableLine($table)
            ];

            try {
                if (!$this->validation->run($post, 'internal')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    if (empty($post['md_supplier_id']))
                        unset($post['md_supplier_id']);
                    else
                        $this->entity->setQuotationType("INTERNAL");

                    if (empty($post['md_employee_id']))
                        unset($post['md_employee_id']);
                    else
                        $this->entity->setQuotationType("FREE");

                    $this->entity->fill($post);
                    $this->entity->setGrandTotal(arrSumField('lineamt', $table));

                    if ($this->isNew()) {
                        $this->entity->setDocStatus($this->DOCSTATUS_Drafted);
                        $this->entity->setIsInternalUse('Y');

                        $docNo = $this->model->getInvNumber('isinternaluse', 'Y');
                        $this->entity->setDocumentNo($docNo);
                    }

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
        $employee = new M_Employee($this->request);
        $supplier = new M_Supplier($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();
                $detail = $this->modelDetail->where($this->model->primaryKey, $id)->findAll();

                if (!empty($list[0]->getEmployeeId())) {
                    $rowEmployee = $employee->find($list[0]->getEmployeeId());
                    $list = $this->field->setDataSelect($employee->table, $list, $employee->primaryKey, $rowEmployee->getEmployeeId(), $rowEmployee->getName());
                }

                if (!empty($list[0]->getSupplierId())) {
                    $rowSupplier = $supplier->find($list[0]->getSupplierId());
                    $list = $this->field->setDataSelect($supplier->table, $list, $supplier->primaryKey, $rowSupplier->getSupplierId(), $rowSupplier->getName());
                }

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

    public function processIt()
    {
        $cWfs = new WScenario();
        $mReceipt = new M_Receipt($this->request);

        if ($this->request->isAJAX()) {
            $post = $this->request->getVar();

            $_ID = $post['id'];
            $_DocAction = $post['docaction'];

            $row = $this->model->find($_ID);
            $line = $this->modelDetail->where($this->model->primaryKey, $_ID)->first();
            $receipt = $mReceipt->where('trx_quotation_id', $_ID)
                ->whereIn('docstatus', [$this->DOCSTATUS_Drafted, $this->DOCSTATUS_Completed])->first();

            $menu = $this->request->uri->getSegment(2);

            try {
                if (!empty($_DocAction)) {
                    if ($_DocAction === $row->getDocStatus()) {
                        $response = message('error', true, 'Please reload the Document');
                    } else if ($_DocAction === $this->DOCSTATUS_Completed) {
                        if ($line) {
                            $this->message = $cWfs->setScenario($this->entity, $this->model, $this->modelDetail, $_ID, $_DocAction, $menu, $this->session);
                            $response = message('success', true, $this->message);
                        } else {
                            $this->entity->setDocStatus($this->DOCSTATUS_Invalid);
                            $response = $this->save();
                        }
                    } else if ($_DocAction === $this->DOCSTATUS_Unlock && !$receipt) {
                        $this->entity->setDocStatus($this->DOCSTATUS_Drafted);
                        $response = $this->save();
                    } else if ($receipt && ($_DocAction === $this->DOCSTATUS_Unlock || $_DocAction === $this->DOCSTATUS_Voided)) {
                        $response = message('error', true, 'Cannot be processed');
                    } else {
                        $this->entity->setDocStatus($_DocAction);
                        $response = $this->save();
                    }
                } else {
                    $response = message('error', true, 'Please Choose the Document Action first');
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

                $grandTotal = ($row->grandtotal - $row->lineamt);

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

                $table[] = [
                    $this->field->fieldTable('input', 'text', 'md_product_id', 'text-uppercase', 'required', 'readonly', null, null, $valPro->getName(), 300),
                    $this->field->fieldTable('input', 'text', 'qtyentered', 'number', 'required', null, null, null, $row[1]->qtyentered, 70),
                    $this->field->fieldTable('input', 'text', 'unitprice', 'rupiah', 'required', 'readonly', null, null, $row[2]->unitprice, 125),
                    $this->field->fieldTable('input', 'text', 'lineamt', 'rupiah', 'required', 'readonly', null, null, $row[2]->unitprice, 125),
                    $this->field->fieldTable('input', 'checkbox', 'isspare', null, null, 'readonly', $row[3]->isspare ? 'checked' : null),
                    $this->field->fieldTable('select', null, 'md_employee_id', null, 'required', !empty($row[4]->employee_id) ? 'readonly' : null, null, $dataEmployee, !empty($row[4]->employee_id) ? $row[4]->employee_id : null, 200, 'md_employee_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'specification', null, null, null, null, null, null, 250),
                    $this->field->fieldTable('input', 'text', 'description', null, null, null, null, null, null, 250),
                    $this->field->fieldTable('button', 'button', 'trx_quotation_detail_id')
                ];
            endforeach;
        }

        //? Update
        if (!empty($set) && count($detail) > 0) {
            foreach ($detail as $row) :
                $valPro = $product->find($row->md_product_id);

                $table[] = [
                    $this->field->fieldTable('input', 'text', 'md_product_id', 'text-uppercase', 'required', 'readonly', null, null, $valPro->getName(), 300),
                    $this->field->fieldTable('input', 'text', 'qtyentered', 'number', 'required', null, null, null, $row->qtyentered, 70),
                    $this->field->fieldTable('input', 'text', 'unitprice', 'rupiah', 'required', 'readonly', null, null, $row->unitprice, 125),
                    $this->field->fieldTable('input', 'text', 'lineamt', 'rupiah', 'required', 'readonly', null, null, $row->lineamt, 125),
                    $this->field->fieldTable('input', 'checkbox', 'isspare', null, null, 'readonly', null, null, $row->isspare),
                    $this->field->fieldTable('select', 'text', 'md_employee_id', null, 'required', !empty($row->md_employee_id) ? 'readonly' : null, null, $dataEmployee, $row->md_employee_id, 200, 'md_employee_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'specification', null, null, null, null, null, $row->specification, 250),
                    $this->field->fieldTable('input', 'text', 'description', null, null, null, null, null, $row->description, 250),
                    $this->field->fieldTable('button', 'button', 'trx_quotation_detail_id', null, null, null, null, null, $row->trx_quotation_detail_id)
                ];
            endforeach;
        }

        return json_encode($table);
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
