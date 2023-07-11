<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Depreciation;
use Config\Services;

class Rpt_Depreciation extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Depreciation($this->request);
    }

    public function index()
    {
        return $this->template->render('report/depreciation/v_depreciation');
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

                    $costPerMonth = $value->currentmonth != 0 ? ($value->costdepreciation / $value->currentmonth) : $value->costdepreciation;

                    $row[] = $value->assetcode;
                    $row[] = $value->product;
                    $row[] = format_dmy($value->transactiondate, '-');
                    $row[] = $value->totalyear;
                    $row[] = $value->startyear;
                    $row[] = formatRupiah(round($value->costdepreciation, 2, PHP_ROUND_HALF_UP));
                    $row[] = formatRupiah(round($costPerMonth, 2, PHP_ROUND_HALF_UP));
                    $row[] = formatRupiah(round($value->accumulateddepreciation, 2, PHP_ROUND_HALF_UP));
                    $row[] = formatRupiah(round($value->bookvalue, 2, PHP_ROUND_HALF_UP));
                    $row[] = $value->currentmonth;
                    $row[] = $value->depreciationtype;

                    $data[] = $row;

                endforeach;

                $recordTotal = $this->datatable->countAll($table, $select, $order, $sort, $search);
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
