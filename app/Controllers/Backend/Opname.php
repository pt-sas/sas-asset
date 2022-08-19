<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Inventory;
use Config\Services;

class Opname extends BaseController
{
    protected $validation;
    protected $request;
    protected $cart;

    public function __construct()
    {
        $this->request = Services::request();
        $this->validation = Services::validation();
        $this->cart = Services::cart();
    }

    public function index()
    {
        return $this->template->render('opname/form_opname');
    }

    public function cek()
    {
        $content = $this->cart->contents();
        $data = array();
        $number = 0;
        foreach ($content as $items) {
            $row = array();
            $number++;
            $ID = $items['id'];
            $ROWID = $items['rowid'];

            $row[] = $items['options']['Asset'];
            $row[] = $items['options']['Employee'];
            $row[] = $items['options']['Status'];
            $row[] = '<button type="button" class="btn btn-link btn-danger btn_delete">
            <i class="fa fa-times"></i>
        </button>';
            $data[] = $row;
        }
        $result = array(
            'data' => $data
        );

        return $result;
    }

    public function insert_cart()
    {
        $inventory = new M_Inventory($this->request);

        $assetcode = $this->request->getGet('assetcode');
        $employee = $this->request->getGet('employee');

        $row = $inventory->getAssetDetail($assetcode)->getRow();

        $status = 'Not Matched';

        if ($row->md_employee_id == $employee)
            $status = 'Matched';
        $this->cart->insert(
            [
                'id'      => $row->trx_inventory_id,
                'qty'     => 0,
                'price'   => 0,
                'name'    => 'T-Shirt4',
                'options' => [
                    'Asset'         => $row->assetcode,
                    'Employee'      => $row->employee,
                    'Status'        => $status
                ]
            ]
        );

        $contents = $this->cek();

        return json_encode($contents);
    }

    public function edit()
    {
        // $this->cart->update(array(
        //     'rowid'   => '68afdf4aa4d7e6551d9eac132c008749',
        //     'id'      => 'sku_1234ABCD',
        //     'qty'     => 5,
        //     'price'   => '24.89',
        //     'name'    => 'T-Shirt',
        //     'options' => array('Size' => 'L', 'Color' => 'Red')
        // ));

        $this->cart->destroy();
    }

    // public function getSeqCode()
    // {
    //     if ($this->request->isAJAX()) {
    //         try {
    //             $docNo = $this->model->getInvNumber();
    //             $response = message('success', true, $docNo);
    //         } catch (\Exception $e) {
    //             $response = message('error', false, $e->getMessage());
    //         }

    //         return $this->response->setJSON($response);
    //     }
    // }
}
