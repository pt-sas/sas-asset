<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTrxOpnameTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'trx_opname_id'         => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'documentno'            => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => false],
            'opnamedate'            => ['type' => 'timestamp', 'null' => false],
            'md_branch_id'          => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'md_room_id'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'md_employee_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'docstatus'             => ['type' => 'CHAR', 'constraint' => 2, 'null' => false],
            'startdate'             => ['type' => 'timestamp', 'null' => false],
            'enddate'               => ['type' => 'timestamp', 'null' => false],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false]
        ]);
        $this->forge->addKey('trx_opname_id', true);
        $this->forge->createTable('trx_opname', true);
    }

    public function down()
    {
        $this->forge->dropTable('trx_opname', true);
    }
}
