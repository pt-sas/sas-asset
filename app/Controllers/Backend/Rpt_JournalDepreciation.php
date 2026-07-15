<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Depreciation;
use App\Models\M_DepreciationDetail;
use App\Models\M_GroupAssetAccount;
use Config\Services;

class Rpt_JournalDepreciation extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Depreciation($this->request);
        $this->modelDetail = new M_DepreciationDetail($this->request);
    }

    public function index()
    {
        $data = ["month" => date('M-Y')];

        return $this->template->render('report/journal_depreciation_montly/v_journal_depreciation_montly', $data);
    }

    public function showAll()
    {
        $post = $this->request->getVar();
        $data = [];

        $recordTotal = 0;
        $recordsFiltered = 0;

        if ($this->request->getMethod(true) === 'POST') {
            if (isset($post['form']) && $post['clear'] === 'false') {

                //* Get All Cost Depreciation
                $mGroupAssetAcct = new M_GroupAssetAccount($this->request);
                $costDepreAll = [];
                foreach ($mGroupAssetAcct->getGroupAssetAccount() as $val) {
                    $costDepreAll[$val->md_groupasset_id][$val->name] = $val;
                }

                $table = $this->modelDetail->table;
                $select = $this->modelDetail->getSelectRptJournal();
                $join = $this->modelDetail->getRptJournalJoin();
                $order = $this->request->getPost('columns');
                $sort = ['assetcode', 'ASC'];
                $search = $this->request->getPost('search');

                $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search, $join);

                foreach ($list as $value) :
                    if (isset($costDepreAll[$value->md_groupasset_id])) {
                        $costDepre = $costDepreAll[$value->md_groupasset_id];

                        //* Debit
                        $row = [];
                        $row[] = $value->assetcode;
                        $row[] = $value->period;
                        $row[] = $costDepre['Depreciation Cost']->coa;
                        $row[] = formatRupiah($value->costdepreciation);
                        $row[] = '';

                        $data[] = $row;

                        //* Credit
                        $row = [];
                        $row[] = $value->assetcode;
                        $row[] = $value->period;
                        $row[] = $costDepre['Accumulation Depreciation']->coa;
                        $row[] = '';
                        $row[] = formatRupiah($value->costdepreciation);

                        $data[] = $row;
                    }

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
