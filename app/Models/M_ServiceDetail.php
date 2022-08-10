<?php

namespace App\Models;

use CodeIgniter\Model;

class M_ServiceDetail extends Model
{
    protected $table      = 'trx_service_detail';
    protected $primaryKey = 'trx_service_detail_id';
    protected $allowedFields = [
        'trx_service_id',
        'assetcode',
        'qtyentered',
        'unitprice',
        'lineamt',
        'description',
        'md_status_id'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\ServiceDetail';
    protected $db;
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
        $this->builder = $this->db->table($this->table);
    }

    public function create($post)
    {
        $table = json_decode($post['table']);

        $result = false;

        $sumPrice = 0;

        foreach ($table as $row) :
            $data = [
                'assetcode'         => $row[0]->assetcode,
                'md_product_id'     => $row[1]->product_id,
                'qtyentered'        => 1,
                'unitprice'         => replaceFormat($row[2]->unitprice),
                'md_status_id'      => $row[3]->status_id,
                'description'       => $row[4]->desc,
                'trx_service_id'    => $post['trx_service_id']
            ];

            if (!empty($row[5]->delete)) {
                $result = $this->builder->where($this->primaryKey, $row[5]->delete)->update($data);
            } else {
                $result = $this->builder->insert($data);
            }

            $sumPrice += replaceFormat($row[2]->unitprice);

            // Update grand total service header
            if ($result) {
                $tableHeader = $this->db->table('trx_service');

                $arrData = [
                    'grandtotal' => $sumPrice
                ];

                $tableHeader->where('trx_service_id', $post['trx_service_id'])->update($arrData);
            }
        endforeach;

        return $result;
    }
}
