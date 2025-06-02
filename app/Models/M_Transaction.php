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

    public function getSelect()
    {
        $sql = "{$this->table}.assetcode,
                p.name AS product,
                {$this->table}.transactiondate,
                {$this->table}.transactiontype,
                r.name AS room,
                e.name AS employee,
                re.documentno AS docreceipt,
                me.documentno AS docmovement,
                u.username AS created,
                r.description";
        return $sql;
    }

    public function getJoin()
    {
        $sql = [
            $this->setDataJoin('trx_inventory i', "i.assetcode = {$this->table}.assetcode", 'inner'),
            $this->setDataJoin('md_product p', "p.md_product_id = {$this->table}.md_product_id", 'left'),
            $this->setDataJoin('md_room r', "r.md_room_id = {$this->table}.md_room_id", 'left'),
            $this->setDataJoin('md_employee e', "e.md_employee_id = {$this->table}.md_employee_id", 'left'),
            $this->setDataJoin('trx_receipt_detail rd', "rd.trx_receipt_detail_id = {$this->table}.trx_receipt_detail_id", 'left'),
            $this->setDataJoin('trx_receipt re', 'rd.trx_receipt_id = re.trx_receipt_id', 'left'),
            $this->setDataJoin('trx_movement_detail md', "md.trx_movement_detail_id = {$this->table}.trx_movement_detail_id", 'left'),
            $this->setDataJoin('trx_movement me', 'md.trx_movement_id = me.trx_movement_id', 'left'),
            $this->setDataJoin('sys_user u', "u.sys_user_id = {$this->table}.created_by", 'left')
        ];

        return $sql;
    }

    private function setDataJoin($tableJoin, $columnJoin, $typeJoin = "inner")
    {
        return [
            "tableJoin" => $tableJoin,
            "columnJoin" => $columnJoin,
            "typeJoin" => $typeJoin
        ];
    }
}
