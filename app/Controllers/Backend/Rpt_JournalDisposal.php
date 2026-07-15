<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Account;
use App\Models\M_Disposal;
use App\Models\M_DisposalDetail;
use App\Models\M_GroupAssetAccount;
use Config\Services;

class Rpt_JournalDisposal extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_DisposalDetail($this->request);
    }

    public function index()
    {
        $data = ["month" => date('M-Y')];

        return $this->template->render('report/journal_disposal_asset/v_journal_disposal_asset', $data);
    }

    public function showAll()
    {
        $post = $this->request->getVar();
        $data = [];

        $recordTotal = 0;
        $recordsFiltered = 0;

        if ($this->request->getMethod(true) === 'POST') {
            if (isset($post['form']) && $post['clear'] === 'false') {
                //* Get All Group Asset COA
                $mGroupAssetAcct = new M_GroupAssetAccount($this->request);
                $costDepreAll = [];
                foreach ($mGroupAssetAcct->getGroupAssetAccount() as $val) {
                    $costDepreAll[$val->md_groupasset_id][$val->name] = $val;
                }

                //* Get Asset Write-Off Loss COA
                $mAccount = new M_Account($this->request);
                $assetWriteOffLossCOA = $mAccount->where('name', 'Rugi Penghapusan Asset Perusahaan')->first();

                $whereClause = "disposal.docstatus = '{$this->DOCSTATUS_Completed}'
                                AND disposal.disposaltype = 'WR'";

                //* Set Where Clause from filter on Form
                if (isset($post['form'])) {
                    foreach ($post['form'] as $val) {
                        if ($val['name'] == 'period' && !empty($val['value'])) {
                            $period = date('Y-m', strtotime($val['value']));
                            $whereClause .= " AND disposal.period = '{$period}'";
                        }

                        if ($val['name'] == 'assetcode' && !empty($val['value'])) {
                            $assetcode = "'" . implode("','", $val['value']) . "'";
                            $whereClause .= " AND trx_disposal_detail.assetcode IN ({$assetcode})";
                        }

                        if ($val['name'] == 'md_groupasset_id' && !empty($val['value'])) {
                            $groupasset = implode(",", $val['value']);
                            $whereClause .= " AND trx_inventory.md_groupasset_id IN ({$groupasset})";
                        }
                    }
                }

                //* Get Disposal Data
                $list = $this->model->getJournalDisposal($whereClause);

                foreach ($list as $value) :
                    if (isset($costDepreAll[$value->md_groupasset_id])) {
                        $costDepre = $costDepreAll[$value->md_groupasset_id];

                        //* Akumulasi Depresiasi
                        $row = [];
                        $row[] = $value->assetcode;
                        $row[] = $value->period;
                        $row[] = $costDepre['Accumulation Depreciation']->coa;
                        $row[] = !empty($value->accumulateddepreciation) ? formatRupiah($value->accumulateddepreciation) : formatRupiah(0);
                        $row[] = '';

                        $data[] = $row;

                        //* Rugi penghapusan aset perusahaan
                        $row = [];
                        $row[] = $value->assetcode;
                        $row[] = $value->period;
                        $row[] = $assetWriteOffLossCOA->value . '_' . $assetWriteOffLossCOA->name;
                        $row[] = !empty($value->accumulateddepreciation) ? formatRupiah($value->unitprice - $value->accumulateddepreciation) : formatRupiah($value->unitprice);
                        $row[] = '';

                        $data[] = $row;

                        //* Credit Asset
                        $row = [];
                        $row[] = $value->assetcode;
                        $row[] = $value->period;
                        $row[] = $costDepre['Asset']->coa;
                        $row[] = '';
                        $row[] = formatRupiah($value->unitprice);

                        $data[] = $row;
                    }

                endforeach;

                $recordTotal = count($list);
                $recordsFiltered = $recordTotal;
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
