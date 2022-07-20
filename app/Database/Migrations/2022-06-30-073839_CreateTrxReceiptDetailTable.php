<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTrxReceiptDetailTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'trx_receipt_detail_id' => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'trx_receipt_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'assetcode'             => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
            'md_product_id'         => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'qtyentered'            => ['type' => 'INT', 'constraint' => 10, 'null' => false],
            'unitprice'             => ['type' => 'DOUBLE', 'null' => false],
            'md_branch_id'          => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'md_division_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'md_room_id'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'md_employee_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'isspare'               => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'trx_quotation_detail_id'   => ['type' => 'INT', 'constraint' => 6, 'null' => false],
        ]);
        $this->forge->addKey('trx_receipt_detail_id', true);
        $this->forge->createTable('trx_receipt_detail', true);
    }

    public function down()
    {
        $this->forge->dropTable('trx_receipt_detail', true);
    }
}
