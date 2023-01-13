<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWEventAuditTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'sys_wfevent_audit_id'  => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'sys_wfactivity_id'     => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'sys_wfresponsible_id'  => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'sys_user_id'           => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'state'                 => ['type' => 'CHAR', 'constraint' => 2, 'null' => false],
            'oldvalue'              => ['type' => 'VARCHAR', 'constraint' => 2000, 'null' => true],
            'newvalue'              => ['type' => 'VARCHAR', 'constraint' => 2000, 'null' => true],
            'isapproved'            => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'N'],
            'textmsg'               => ['type' => 'VARCHAR', 'constraint' => 2000, 'null' => true],
            'table'                 => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => false],
            'record_id'             => ['type' => 'INT', 'constraint' => 6, 'null' => false]
        ]);

        $this->forge->addKey('sys_wfevent_audit_id', true);
        $this->forge->createTable('sys_wfevent_audit', true);
    }

    public function down()
    {
        $this->forge->dropTable('sys_wfevent_audit', true);
    }
}
