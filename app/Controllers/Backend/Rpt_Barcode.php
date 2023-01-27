<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Inventory;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Dompdf\Dompdf;
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
        return $this->template->render('report/barcode/v_barcode');
    }

    public function showAll()
    {
        $dompdf = new Dompdf();
        $writer = new PngWriter();

        $list = $this->model->getInventory()->getResult();

        $g = count($list);

        foreach ($list as $key => $row) :
            // Create QR code
            $qrCode = QrCode::create($row->assetcode)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                ->setSize(85)
                ->setMargin(2)
                ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255));

            // // Create generic logo
            // $logo = Logo::create(FCPATH . 'custom/image/logo.png')
            //     ->setResizeToWidth(100);

            // // Create generic label
            // $label = Label::create('PR00001')
            //     ->setTextColor(new Color(255, 0, 0));

            $result = $writer->write($qrCode);

            $data[] = [
                'qr'        => $result->getDataUri(),
                'branch'    => $row->branch,
                'product'   => $row->product,
                'assetcode' => $row->assetcode,
                'date'      => date('d-M-Y'),
                'list'      => count($list)
            ];
        endforeach;

        $f = [
            'data' => $data
        ];

        // return view('report/barcode/qrcode', $f);
        $html = view('report/barcode/qrcode', $f);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'potrait');
        $dompdf->render();
        $dompdf->stream("QRCode.pdf", array("Attachment" => false));

        // return json_encode($this->request->getPost());
        // return json_encode($list);
    }
}
