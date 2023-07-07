<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_DepreciationDetail;
use Config\Services;

class Rpt_DepreciationDetail extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_DepreciationDetail($this->request);
    }

    public function index()
    {
        return $this->template->render('report/depreciation_detail/v_depreciation_detail');
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
                $select = $this->model->getSelect();
                $join = $this->model->getJoin();
                $order = $this->request->getPost('columns');
                $sort = ['assetcode', 'ASC'];
                $search = $this->request->getPost('search');

                $number = $this->request->getPost('start');
                $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search, $join);

                foreach ($list as $value) :
                    $row = [];

                    $row[] = $value->assetcode;
                    $row[] = $value->product;
                    $row[] = format_dmy($value->transactiondate, '-');
                    $row[] = $value->totalyear;
                    $row[] = $value->period;
                    $row[] = formatRupiah(round($value->residualvalue, 2, PHP_ROUND_HALF_UP));
                    $row[] = formatRupiah(round($value->costdepreciation, 2, PHP_ROUND_HALF_UP));
                    $row[] = formatRupiah(round($value->accumulateddepreciation, 2, PHP_ROUND_HALF_UP));
                    $row[] = formatRupiah(round($value->bookvalue, 2, PHP_ROUND_HALF_UP));
                    $row[] = $value->currentmonth;
                    $row[] = $value->depreciationtype;

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
}
