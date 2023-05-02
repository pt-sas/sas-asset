<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTrxDisposal extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'trx_disposal_id'       => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'documentno'            => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => false],
            'disposaldate'          => ['type' => 'timestamp', 'null' => false],
            'disposaltype'          => ['type' => 'CHAR', 'constraint' => 2, 'null' => false],
            'grandtotal'            => ['type' => 'DOUBLE', 'null' => false],
            'docstatus'             => ['type' => 'CHAR', 'constraint' => 2, 'null' => false],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'md_supplier_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'sys_wfscenario_id'     => ['type' => 'INT', 'constraint' => 6, 'null' => false],
        ]);
        $this->forge->addKey('trx_disposal_id', true);
        $this->forge->createTable('trx_disposal', true);
    }

    public function down()
    {
        $this->forge->dropTable('trx_disposal', true);
    }
}
