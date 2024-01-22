<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Inventory;
use App\Models\M_Product;
use App\Models\M_Branch;
use App\Models\M_Division;
use App\Models\M_Employee;
use App\Models\M_Room;
use App\Models\M_Status;
use Config\Services;

class Inventory extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Inventory($this->request);
        $this->entity = new \App\Entities\Inventory();
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
            'default_logic' => json_decode($this->defaultLogic()),
        ];

        return $this->template->render('transaction/inventory/v_inventory', $data);
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
                $ID = $value->trx_inventory_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->assetcode;
                $row[] = $value->numberplate;
                $row[] = $value->product;
                $row[] = format_dmy($value->inventorydate, '-');
                $row[] = formatRupiah($value->unitprice);
                $row[] = $value->branch;
                $row[] = $value->division;
                $row[] = $value->room;
                $row[] = $value->employee;
                $row[] = $value->status;
                $row[] = active($value->isspare);
                $row[] = $value->description;
                $row[] = active($value->isactive);
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

                if ($this->isNew()) {
                    $this->entity->setQtyEntered(1);
                }

                if (!$this->validation->run($post, 'inventory')) {
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
        $product = new M_Product($this->request);
        $branch = new M_Branch($this->request);
        $employee = new M_Employee($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();

                $rowProduct = $product->find($list[0]->getProductId());
                $rowBranch = $branch->find($list[0]->getBranchId());
                $rowEmployee = $employee->find($list[0]->getEmployeeId());

                $list = $this->field->setDataSelect($product->table, $list, $product->primaryKey, $rowProduct->getProductId(), $rowProduct->getName());
                $list = $this->field->setDataSelect($branch->table, $list, $branch->primaryKey, $rowBranch->getBranchId(), $rowBranch->getName());
                $list = $this->field->setDataSelect($employee->table, $list, $employee->primaryKey, $rowEmployee->getEmployeeId(), $rowEmployee->getName());

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

    public function getAssetDetail()
    {
        $product = new M_Product($this->request);
        $branch = new M_Branch($this->request);
        $employee = new M_Employee($this->request);
        $division = new M_Division($this->request);
        $room = new M_Room($this->request);

        if ($this->request->getMethod(true) === 'POST') {
            $post = $this->request->getVar();

            try {
                $list = [];

                if (!empty($post['assetcode'])) {
                    $list = $this->model->where('assetcode', $post['assetcode'])->findAll();

                    $rowProduct = $product->find($list[0]->getProductId());
                    $rowBranch = $branch->find($list[0]->getBranchId());
                    $rowEmployee = $employee->find($list[0]->getEmployeeId());
                    $rowDivision = $division->find($list[0]->getDivisionId());
                    $rowRoom = $room->find($list[0]->getRoomId());

                    $roomName = $rowRoom->getName() . " (" . $rowRoom->getDescription() . ")";

                    $list = $this->field->setDataSelect($product->table, $list, $product->primaryKey, $rowProduct->getProductId(), $rowProduct->getName());
                    $list = $this->field->setDataSelect($branch->table, $list, "branch_from", $rowBranch->getBranchId(), $rowBranch->getName());
                    $list = $this->field->setDataSelect($employee->table, $list, "employee_from", $rowEmployee->getEmployeeId(), $rowEmployee->getName());
                    $list = $this->field->setDataSelect($division->table, $list, "division_from", $rowDivision->getDivisionId(), $rowDivision->getName());
                    $list = $this->field->setDataSelect($room->table, $list, "room_from", $rowRoom->getRoomId(), $roomName);

                    $response = message('success', true, $list);
                } else {
                    $response = message('success', false, $list);
                }
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function getAssetCode()
    {
        if ($this->request->isAjax()) {
            $post = $this->request->getVar();

            $response = [];

            try {
                if (isset($post['search'])) {
                    $list = $this->model->where('isactive', 'Y')
                        ->like('assetcode', $post['search'])
                        ->orLike('numberplate', $post['search'])
                        ->orderBy('assetcode', 'ASC')
                        ->findAll();
                } else {
                    $list = $this->model->where('isactive', 'Y')
                        ->orderBy('assetcode', 'ASC')
                        ->findAll();
                }

                foreach ($list as $key => $row) :
                    $response[$key]['id'] = $row->getAssetCode();

                    if (isset($post['plate']) && $row->getNumberPlate())
                        $response[$key]['text'] = $row->getAssetCode() . " - " . $row->getNumberPlate();
                    else
                        $response[$key]['text'] = $row->getAssetCode();
                endforeach;
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function defaultLogic()
    {
        $result = [];

        //! default logic for dropdown md_status_id
        $role = $this->access->getUserRoleName($this->access->getSessionUser(), 'W_Not_Default_Status');

        if (!$role) {
            $result = [
                'field'         => 'md_status_id',
                'id'            => 100000, //Aset
                'condition'     => true
            ];
        }

        return json_encode($result);
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
}
