<?php

namespace App\Models;

use CodeIgniter\Model;

class M_Transaction extends Model
{
    protected $table            = 'md_transaction';
    protected $primaryKey       = 'md_transaction_id';
    protected $useTimestamps    = true;
    protected $returnType       = 'App\Entities\Transaction';
    protected $db;
    protected $builder;

    public function __construct()
    {
        parent::__construct();
        $this->db = db_connect();
        $this->builder = $this->db->table($this->table);
    }

    public function create($arrData)
    {
        $result = false;

        foreach ($arrData as $row) :
            $data = [
                'assetcode'                 => $row->assetcode,
                'md_product_id'             => $row->md_product_id,
                'transactiontype'           => $row->transactiontype,
                'transactiondate'           => $row->transactiondate,
                'md_employee_id'            => $row->md_employee_id,
                'md_room_id'                => $row->md_room_id,
                'qtyentered'                => $row->qtyentered,
                'created_by'                => session()->get('sys_user_id'),
                'updated_by'                => session()->get('sys_user_id')
            ];

            if (isset($row->trx_inventory_id))
                $data['trx_inventory_id'] = $row->trx_inventory_id;

            if (isset($row->trx_receipt_detail_id))
                $data['trx_receipt_detail_id'] = $row->trx_receipt_detail_id;

            if (isset($row->trx_movement_detail_id))
                $data['trx_movement_detail_id'] = $row->trx_movement_detail_id;

            $result = $this->builder->insert($data);
        endforeach;

        return $result;
    }
}
