<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Inventory;
use TCPDF;
use Config\Services;

class Rpt_Barcode extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Inventory($this->request);
    }

    public function index()
    {
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');

        $data = [
            'date_range' => $start_date . ' - ' . $end_date
        ];

        return $this->template->render('report/barcode/v_barcode', $data);
    }

    public function showAll()
    {
        $post = $this->request->getVar();
        $data = [];

        $recordTotal = 0;
        $recordsFiltered = 0;

        if ($this->request->getMethod(true) === 'POST') {
            if (isset($post['form']) && $post['clear'] === 'false') {
                $table = $this->model->table;
                $select = $this->model->getSelectDetail();
                $join = $this->model->getJoinDetail();
                $order = $this->request->getPost('columns');
                $sort = $this->model->order;
                $search = $this->request->getPost('search');

                $number = $this->request->getPost('start');
                $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search, $join);

                foreach ($list as $value) :
                    $row = [];

                    $checkbox = '<div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input check-data" name="ischeck" value="' . $value->assetcode . '" checked>
                                        <span class="form-check-sign"></span>
                                    </label>
                                </div>';

                    $row[] = $checkbox;
                    $row[] = $value->assetcode;
                    $row[] = $value->receipt;
                    $data[] = $row;

                endforeach;

                $recordTotal = $this->datatable->countAll($table);
                $recordsFiltered = $this->datatable->countFiltered($table, $select, $order, $sort, $search, $join);
            }

            $result = [
                'draw'              => $this->request->getPost('draw'),
                'recordsTotal'      => $recordTotal,
                'recordsFiltered'   => $recordsFiltered,
                'data'              => $data
            ];

            return $this->response->setJSON($result);
        }
    }


    public function print()
    {
        $post = $this->request->getPost();

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

        $list = json_decode($post['assetcode']);

        foreach ($list as $key => $value) :
            if ($key % 2 == 0) {
                $pdf->write2DBarcode($value, 'QRCODE,L', 20, -5, 100, 100, $style, 'N');
                $pdf->StartTransform();
                $pdf->Rotate(90, 130, 90);
                $pdf->Text(135, 80, $value);
                $pdf->StopTransform();
            } else {
                $pdf->write2DBarcode($value, 'QRCODE,L', 170, -5, 100, 100, $style, 'N');
                $pdf->StartTransform();
                $pdf->Rotate(90, 280, 90);
                $pdf->Text(285, 80, $value);
                $pdf->StopTransform();

                $key += 1;
                $totalData = count($list);

                if ($key < $totalData)
                    $pdf->AddPage();
            }
        endforeach;

        $path = FCPATH . 'uploads/';

        if (!is_dir($path))
            mkdir($path);

        $fileName = 'qrcode_' . date('YmdHis') . '.pdf';

        $pdf->Output($path . $fileName, 'F');

        $path = base_url('uploads') . '/' . $fileName;

        return json_encode($path);
    }
}
