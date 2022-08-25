<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTrxReceiptTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'trx_receipt_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'trx_quotation_id'      => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'documentno'            => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => false],
            'receiptdate'           => ['type' => 'timestamp', 'null' => false],
            'grandtotal'            => ['type' => 'DOUBLE', 'null' => false],
            'docstatus'             => ['type' => 'CHAR', 'constraint' => 2, 'null' => false],
            'invoiceno'             => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'md_status_id'          => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'md_supplier_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => true],
        ]);
        $this->forge->addKey('trx_receipt_id', true);
        $this->forge->createTable('trx_receipt', true);
    }

    public function down()
    {
        $this->forge->dropTable('trx_receipt', true);
    }
}
