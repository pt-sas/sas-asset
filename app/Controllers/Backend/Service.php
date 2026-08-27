<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Service;
use App\Models\M_ServiceDetail;
use App\Models\M_Product;
use App\Models\M_Status;
use App\Models\M_Supplier;
use App\Models\M_Inventory;
use App\Models\M_Room;
use App\Models\M_ServicePart;
use Config\Services;

class Service extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Service($this->request);
        $this->modelDetail = new M_ServiceDetail($this->request);
        $this->subModelDetail = new M_ServicePart($this->request);
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
                $row[] = $this->template->tableButton($ID, $value->docstatus, 'SERVICE');
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

            $table = json_decode($post['table']);

            //! Mandatory property for detail validation
            $post['line'] = countLine($table);
            $post['detail'] = [
                'table' => arrTableLine($table)
            ];

            try {
                if (!$this->validation->run($post, 'service')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    $this->entity->fill($post);

                    //* Sum if status not Service Canceled
                    $arrTableSum = [];
                    foreach ($table as $item) {
                        if ($item->md_status_id != 100006) $arrTableSum[] = $item;
                    }

                    $this->entity->setGrandTotal(arrSumField('lineamt', $arrTableSum));

                    if ($this->isNew()) {
                        $this->entity->setDocStatus($this->DOCSTATUS_Drafted);

                        $docNo = $this->model->getInvNumber();
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
        $mSupplier = new M_Supplier($this->request);
        $mRoom = new M_Room($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();
                $detail = $this->modelDetail->where($this->model->primaryKey, $id)->findAll();

                $rowSupplier = $mSupplier->find($list[0]->getSupplierId());

                $list = $this->field->setDataSelect($mSupplier->table, $list, $mSupplier->primaryKey, $rowSupplier->getSupplierId(), $rowSupplier->getName());

                $rowRoom = $mRoom->find($list[0]->getRoomId());

                $list = $this->field->setDataSelect($mRoom->table, $list, $mRoom->primaryKey, $rowRoom->getRoomId(), $rowRoom->getName());

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
                $result = $this->delete($id);
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
        $cWfs = new WScenario();
        if ($this->request->isAJAX()) {
            $post = $this->request->getVar();

            $_ID = $post['id'];
            $_DocAction = $post['docaction'];

            $row = $this->model->find($_ID);
            $serviceOnDelivery = $this->modelDetail->where($this->model->primaryKey, $_ID)
                ->whereIn('md_status_id', [100004, 100005])
                ->first();

            try {
                $menu = $this->request->uri->getSegment(2);

                if (!empty($_DocAction)) {
                    if ($_DocAction === $row->getDocStatus()) {
                        $response = message('error', true, 'Please reload the Document');
                    } else if ($_DocAction === $this->DOCSTATUS_Inprogress) {
                        $this->message = $cWfs->setScenario($this->entity, $this->model, $this->modelDetail, $_ID, $this->DOCSTATUS_Completed, $menu, $this->session);
                        $response = message('success', true, $this->message);
                    } else if ($_DocAction === $this->DOCSTATUS_Completed) {
                        if (is_null($serviceOnDelivery)) {
                            $this->entity->setDocStatus($this->DOCSTATUS_Completed);
                            $response = $this->save();
                        } else {
                            $response = message('error', true, 'Please change the status on line');
                        }
                    } else if ($_DocAction === $this->DOCSTATUS_Prepare) {
                        $this->entity->setDocStatus($this->DOCSTATUS_Prepare);
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

    public function tableLine($set = null, $detail = [])
    {
        $mInventory = new M_Inventory($this->request);
        $mProduct = new M_Product($this->request);
        $mStatus = new M_Status($this->request);

        $post = $this->request->getVar();

        $uri = $this->request->uri->getSegment(2);

        $dataProduct = $mProduct->where('isactive', 'Y')->findAll();

        $table = [];

        $subQuery = $this->model->builder->select('trx_service_detail.assetcode')
            ->join('trx_service_detail', 'trx_service_detail.trx_service_id = trx_service.trx_service_id')
            ->where('trx_service_detail.assetcode = trx_inventory.assetcode')
            ->whereIn('trx_service_detail.md_status_id', [100004, 100005])
            ->getCompiledSelect();

        //? Create
        if (empty($set)) {
            $dataStatus = $mStatus->where([
                'isactive'  => 'Y',
                'isline'    => 'Y'
            ])->like('menu_id', $uri, 'both')
                ->orderBy('name', 'ASC')
                ->findAll();

            $dataInventory = $mInventory->where('isactive', 'Y')
                ->where('md_room_id', $post['md_room_id'])
                ->where("NOT EXISTS ($subQuery)", null, false) // Raw SQL for NOT EXISTS
                ->orderBy('assetcode', 'ASC')
                ->findAll();

            $table = [
                $this->field->fieldTable('select', null, 'assetcode', 'unique', 'required', null, null, $dataInventory, null, 200, 'assetcode', 'assetcode'),
                $this->field->fieldTable('select', null, 'md_product_id', null, 'required', 'readonly', null, $dataProduct, null, 300, 'md_product_id', 'name'),
                "",
                $this->field->fieldTable('input', 'text', 'lineamt', 'number', 'required', 'readonly', null, null, null, 250),
                $this->field->fieldTable('select', null, 'md_status_id', null, 'required', 'readonly', null, $dataStatus, 'On Delivery', 150, 'md_status_id', 'name'),
                "",
                $this->field->fieldTable('input', 'text', 'description', null, null, null, null, null, null, 250),
                $this->field->fieldTable('button', 'button', 'trx_service_detail_id')
            ];
        }

        //? Update
        if (!empty($set) && count($detail) > 0) {
            $service = $this->model->find($detail[0]->trx_service_id);

            foreach ($detail as $row) :
                if ($row->isagree === 'Y' || $service->getDocStatus() == $this->DOCSTATUS_Completed) {
                    $status = [100006, 100004, 100011];
                } else {
                    $status = [100006, 100005];
                }

                $dataStatus = $mStatus->where([
                    'isactive'  => 'Y',
                    'isline'    => 'Y'
                ])->whereIn('md_status_id', $status)
                    ->like('menu_id', $uri, 'both')
                    ->orderBy('name', 'ASC')
                    ->findAll();
                $dataInventory = $mInventory->where('isactive', 'Y')
                    ->where('md_room_id', $service->md_room_id)
                    ->orderBy('assetcode', 'ASC')
                    ->findAll();

                if ($service->getDocStatus() === $this->DOCSTATUS_Prepare || ($service->getDocStatus() === $this->DOCSTATUS_Inprogress && $row->isagree != null) || $service->getDocStatus() === $this->DOCSTATUS_Completed) {
                    $btnDetail = '<div class="form-group">';
                    $btnDetail .= '<button type="button" title="Detail" class="btn btn-sm btn-round line btn-info btn_isdetail numeric" id="" name="isdetail" value="0">';
                    $btnDetail .= '<i class="fas fa-th-list"></i>';
                    $btnDetail .= '</button>';
                    $btnDetail .= '</div>';
                } else {
                    $btnDetail = "";
                }

                $updateable = null;

                if ($service->getDocStatus() === $this->DOCSTATUS_Prepare || ($service->getDocStatus() === $this->DOCSTATUS_Inprogress && $row->isagree != null)) {
                    $updateable = 'updatable';
                }

                $table[] = [
                    $this->field->fieldTable('select', null, 'assetcode', 'unique', 'required', null, null, $dataInventory, $row->assetcode, 200, 'assetcode', 'assetcode'),
                    $this->field->fieldTable('select', null, 'md_product_id', null, 'required', 'readonly', null, $dataProduct, $row->md_product_id, 300, 'md_product_id', 'name'),
                    $btnDetail,
                    $this->field->fieldTable('input', 'text', 'lineamt', 'rupiah', 'required', 'readonly', null, null, $row->lineamt, 250),
                    $this->field->fieldTable('select', null, 'md_status_id', $updateable, 'required', $service->getDocStatus() === $this->DOCSTATUS_Drafted ? 'readonly' : null, null, $dataStatus, $row->md_status_id, 150, 'md_status_id', 'name'),
                    statusRealize($row->isagree),
                    $this->field->fieldTable('input', 'text', 'description', $updateable, null, null, null, null, $row->description, 250),
                    $this->field->fieldTable('button', 'button', 'trx_service_detail_id', null, null, null, null, null, $row->trx_service_detail_id)
                ];
            endforeach;
        }

        return json_encode($table);
    }

    public function destroyAllLine()
    {
        if ($this->request->isAJAX()) {
            $post = $this->request->getVar();

            try {
                $result = $this->modelDetail->where($this->model->primaryKey, $post['trx_service_id'])->first();

                //? Exists data movement detail
                if ($result)
                    $result = $this->modelDetail->where($this->model->primaryKey, $post['trx_service_id'])->delete();

                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }
}
