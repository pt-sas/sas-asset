<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;

use App\Models\M_Service;
use App\Models\M_ServiceDetail;
use App\Models\M_Product;
use App\Models\M_Status;
use App\Models\M_Supplier;

use Config\Services;

class Service extends BaseController
{
    protected $table = 'trx_service';

    public function index()
    {
        $request = Services::request();

        $supplier = new M_Supplier($request);

        $data = [
            'today'     => date('Y-m-d'),
            'supplier'  => $supplier->where(
                [
                    'isactive'  => 'Y',
                    'isservice' => 'Y'
                ]
            )->findAll()
        ];

        return $this->template->render('transaction/service/v_service', $data);
    }

    public function showAll()
    {
        $request = Services::request();
        $service = new M_Service($request);

        if ($request->getMethod(true) === 'POST') {
            $lists = $service->getDatatables();
            $data = [];
            $number = $request->getPost('start');

            foreach ($lists as $value) :
                $row = [];
                $ID = $value->trx_service_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->documentno;
                $row[] = format_dmy($value->servicedate);
                $row[] = $value->supplier;
                $row[] = $value->description;
                $row[] = $this->template->tableButton($ID);
                $data[] = $row;
            endforeach;

            $result = [
                'draw'              => $request->getPost('draw'),
                'recordsTotal'      => $service->countAll(),
                'recordsFiltered'   => $service->countFiltered(),
                'data'              => $data
            ];

            return json_encode($result);
        }
    }

    public function create()
    {
        $request = Services::request();
        $validation = Services::validation();
        $eService = new \App\Entities\Service();

        $service = new M_Service($request);
        $serviceDetail = new M_ServiceDetail();

        $post = $request->getVar();

        $table = json_decode($post['table']);

        // Mandatory property for validation detail
        $post['line'] = countLine(count($table));
        $post['detail'] = [
            'table' => arrTableLine($table)
        ];

        try {
            $eService->fill($post);

            if (!$validation->run($post, 'service')) {
                $response = $this->field->errorValidation($this->table, $post);
            } else {
                $result = $service->save($eService);

                $post['trx_service_id'] = $service->insertID();

                $serviceDetail->create($post);

                $msg = $result ? 'Your data has been Updated successfully !' : $result;

                $response = message('success', true, $msg);
            }
        } catch (\Exception $e) {
            $response = message('error', false, $e->getMessage());
        }

        return json_encode($response);
    }

    public function show($id)
    {
        $request = Services::request();
        $service = new M_Service($request);
        $serviceDetail = new M_ServiceDetail();

        try {
            $list = $service->where('trx_service_id', $id)->findAll();
            $detail = $serviceDetail->where('trx_service_id', $id)->findAll();

            $result = [
                'header'    => $this->field->store($this->table, $list),
                'line'      => $this->tableLine('edit', $detail)
            ];

            $response = message('success', true, $result);
        } catch (\Exception $e) {
            $response = message('error', false, $e->getMessage());
        }

        return json_encode($response);
    }

    public function edit()
    {
        $request = Services::request();
        $validation = Services::validation();
        $eService = new \App\Entities\Service();

        $service = new M_Service($request);
        $serviceDetail = new M_ServiceDetail();

        $post = $request->getVar();

        $table = json_decode($post['table']);

        // Mandatory property for validation detail
        $post['line'] = countLine(count($table));
        $post['detail'] = [
            'table' => arrTableLine($table)
        ];

        try {
            $eService->fill($post);
            $eService->trx_service_id = $post['id'];

            if (!$validation->run($post, 'service')) {
                $response = $this->field->errorValidation($this->table, $post);
            } else {
                $result = $service->save($eService);

                $post['trx_service_id'] = $post['id'];

                $serviceDetail->create($post);

                $msg = $result ? 'Your data has been Updated successfully !' : $result;

                $response = message('success', true, $msg);
            }
        } catch (\Exception $e) {
            $response = message('error', false, $e->getMessage());
        }

        return json_encode($response);
    }

    public function destroy($id)
    {
        $request = Services::request();
        $service = new M_Service($request);
        $serviceDetail = new M_ServiceDetail();

        try {
            $result = $service->delete($id);

            if ($result) {
                $serviceDetail->where('trx_service_id', $id)->delete();
            }

            $response = message('success', true, $result);
        } catch (\Exception $e) {
            $response = message('error', false, $e->getMessage());
        }

        return json_encode($response);
    }

    public function tableLine($set = null, $detail = [])
    {
        $request = Services::request();
        $uri = $request->uri->getSegment(2);

        $product = new M_Product($request);
        $status = new M_Status($request);

        $dataProduct = $product->where('isactive', 'Y')->findAll();

        $dataStatus = $status->where('isactive', 'Y')
            ->like('menu_id', $uri, 'both')
            ->findAll();

        $table = [];

        if (!empty($set) && count($detail) > 0) {
            foreach ($detail as $row) :
                $table[] = [
                    $this->field->fieldTable('input', 'text', 'assetcode', null, true, null, null, null, $row->assetcode, 170),
                    $this->field->fieldTable('select', null, 'product_id', null, true, null, null, $dataProduct, $row->md_product_id, 200, 'md_product_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'unitprice', 'rupiah', true, null, null, null, $row->unitprice, 150),
                    $this->field->fieldTable('select', null, 'status_id', null, false, null, null, $dataStatus, $row->md_status_id, 200, 'md_status_id', 'name'),
                    $this->field->fieldTable('input', 'text', 'desc', null, false, null, null, null, $row->description, 250),
                    $this->field->fieldTable('button', 'button', 'delete', null, null, null, null, null, $row->trx_service_detail_id)
                ];
            endforeach;
        }

        if (empty($set)) {
            $table = [
                $this->field->fieldTable('input', 'text', 'assetcode', null, 'required', null, null, null, null, 170),
                $this->field->fieldTable('select', null, 'product_id', null, 'required', null, null, $dataProduct, null, 200, 'md_product_id', 'name'),
                $this->field->fieldTable('input', 'text', 'unitprice', 'rupiah', 'required', null, null, null, null, 150),
                $this->field->fieldTable('select', null, 'status_id', null, null, null, null, $dataStatus, null, 200, 'md_status_id', 'name'),
                $this->field->fieldTable('input', 'text', 'desc', null, null, null, null, null, null, 250),
                $this->field->fieldTable('button', 'button', 'delete')
            ];
        }

        return json_encode($table);
    }

    public function destroyLine($id)
    {
        $serviceDetail = new M_ServiceDetail();

        try {
            $result = $serviceDetail->delete($id);
            $response = message('success', true, $result);
        } catch (\Exception $e) {
            $response = message('error', false, $e->getMessage());
        }

        return json_encode($response);
    }
}
