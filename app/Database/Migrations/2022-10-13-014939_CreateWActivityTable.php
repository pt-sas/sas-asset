<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWActivityTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'sys_wfactivity_id'     => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'sys_wfscenario_id'     => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'sys_wfresponsible_id'  => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'sys_user_id'           => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'state'                 => ['type' => 'CHAR', 'constraint' => 2, 'null' => false],
            'processed'             => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'N'],
            'textmsg'               => ['type' => 'VARCHAR', 'constraint' => 2000, 'null' => true],
            'table'                 => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => false],
            'record_id'             => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'menu'                  => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => false]
        ]);

        $this->forge->addKey('sys_wfactivity_id', true);
        $this->forge->createTable('sys_wfactivity', true);
    }

    public function down()
    {
        $this->forge->dropTable('sys_wfactivity', true);
    }
}
