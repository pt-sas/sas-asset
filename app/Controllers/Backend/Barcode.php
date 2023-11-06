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
use TCPDF;

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

    public function getLabelAsset($arrData)
    {
        $width = 300;
        $height = 95;
        $pageLayout = array($width, $height); //  or array($height, $width) 
        $pdf = new TCPDF('l', 'pt', $pageLayout, true, 'UTF-8', false);

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set auto page breaks
        $pdf->SetMargins(PDF_MARGIN_LEFT - 15, PDF_MARGIN_TOP - 29, PDF_MARGIN_RIGHT - 16);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 0);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // add a page
        $pdf->AddPage();

        $pdf->SetFont('helvetica', '', 10);

        // set style for barcode
        $style = array(
            'border' => false,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1, // height of a single module in points
        );

        foreach ($arrData as $key => $value) :
            if ($key % 2 == 0) {
                $pdf->write2DBarcode($value, 'QRCODE,L', 20, -5, 100, 100, $style, 'N');
                $pdf->StartTransform();
                $pdf->Rotate(90, 130, 90);
                $pdf->Text(strlen($value) > 14 ? 127 : 135, 80, $value);
                $pdf->StopTransform();
            } else {
                $pdf->write2DBarcode($value, 'QRCODE,L', 170, -5, 100, 100, $style, 'N');
                $pdf->StartTransform();
                $pdf->Rotate(90, 280, 90);
                $pdf->Text(strlen($value) > 14 ? 277 : 285, 80, $value);
                $pdf->StopTransform();

                $key += 1;
                $totalData = count($arrData);

                if ($key < $totalData)
                    $pdf->AddPage();
            }
        endforeach;

        $path = FCPATH . 'uploads/';

        if (!is_dir($path))
            mkdir($path);

        $fileName = 'qrcode_' . date('YmdHis') . '.pdf';
        $pdf->Output($path . $fileName, 'F');

        return $fileName;
    }
}
