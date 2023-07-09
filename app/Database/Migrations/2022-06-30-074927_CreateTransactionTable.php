<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'md_transaction_id'     => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'assetcode'             => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
            'md_product_id'         => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'transactiontype'       => ['type' => 'CHAR', 'constraint' => 2, 'null' => false],
            'transactiondate'       => ['type' => 'timestamp', 'null' => false],
            'md_room_id'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'md_employee_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'qtyentered'            => ['type' => 'INT', 'constraint' => 10, 'null' => false],
            'trx_inventory_id'      => ['type' => 'INT', 'constraint' => 6, 'null' => true],
            'trx_receipt_detail_id' => ['type' => 'INT', 'constraint' => 6, 'null' => true],
            'trx_movement_detail_id' => ['type' => 'INT', 'constraint' => 6, 'null' => true],
        ]);
        $this->forge->addKey('md_transaction_id', true);
        $this->forge->createTable('md_transaction', true);
    }

    public function down()
    {
        $this->forge->dropTable('md_transaction', true);
    }
}
