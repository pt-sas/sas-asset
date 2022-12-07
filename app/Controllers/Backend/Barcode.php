<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Barcode;
use App\Models\M_Reference;
use Config\Services;
use \Picqer\Barcode\BarcodeGeneratorJPG;
use \Picqer\Barcode\BarcodeGeneratorPNG;
use \Picqer\Barcode\BarcodeGeneratorSVG;
use \Picqer\Barcode\BarcodeGeneratorHTML;

class Barcode extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Barcode($this->request);
        $this->entity = new \App\Entities\Barcode();
    }

    public function index()
    {
        $reference = new M_Reference($this->request);

        $data = [
            'ref_list_generator' => $reference->findBy([
                'sys_reference.name'              => 'GeneratorTypeList',
                'sys_reference.isactive'          => 'Y',
                'sys_ref_detail.isactive'         => 'Y',
            ], null, [
                'field'     => 'sys_ref_detail.name',
                'option'    => 'ASC'
            ])->getResult(),
            'ref_list_barcode' => $reference->findBy([
                'sys_reference.name'              => 'BarcodeTypeList',
                'sys_reference.isactive'          => 'Y',
                'sys_ref_detail.isactive'         => 'Y',
            ], null, [
                'field'     => 'sys_ref_detail.name',
                'option'    => 'ASC'
            ])->getResult(),
            'ref_list_position' => $reference->findBy([
                'sys_reference.name'              => 'PositionTextList',
                'sys_reference.isactive'          => 'Y',
                'sys_ref_detail.isactive'         => 'Y',
            ], null, [
                'field'     => 'sys_ref_detail.name',
                'option'    => 'ASC'
            ])->getResult()
        ];

        return $this->template->render('backend/configuration/barcode/form_barcode', $data);
    }

    public function showAll()
    {
        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->findAll(1);

                $barcode = $this->generateBarcode($list[0]->getText(), $list[0]->getBarcodeType(), $list[0]->getWidthFactor(), $list[0]->getHeight());

                if (in_array($list[0]->barcodetype, ["JPG", "PNG"])) {
                    $barcode = base64_encode($this->generateBarcode($list[0]->getText(), $list[0]->getBarcodeType(), $list[0]->getWidthFactor(), $list[0]->getHeight()));
                }

                $data = [
                    'barcode' => $barcode,
                    'barcodetype' => $list[0]->getBarcodeType(),
                    'text' => $list[0]->getText(),
                    'position' => $list[0]->getPositionText(),
                    'size' => $list[0]->getSizeText()
                ];

                $result = [
                    'header'    => $this->field->store($this->model->table, $list),
                    'barcode'   => $data
                ];

                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function create()
    {
        if ($this->request->getMethod(true) === 'POST') {
            $post = $this->request->getVar();

            try {
                $this->entity->fill($post);

                if (!$this->validation->run($post, 'barcode')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    $response = $this->save();
                    $response[0]['insert_id'] = $this->model->getInsertID();
                }
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    private function generateBarcode(?string $text, $type = "HTML", $width, $height)
    {
        switch ($type) {
            case "JPG":
                header('Content-type: image/jpeg');
                $generator = new BarcodeGeneratorJPG();
                break;
            case "PNG":
                $generator = new BarcodeGeneratorPNG();
                break;
            case "SVG":
                $generator = new BarcodeGeneratorSVG();
                break;
            default:
                $generator = new BarcodeGeneratorHTML();
        }

        return $generator->getBarcode($text, $generator::TYPE_CODE_128, $width, $height);
    }
}
