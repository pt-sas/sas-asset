<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnTableLineForActivityAndEventAudit extends Migration
{
    public function up()
    {
        $field = [
            'tableline' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'recordline_id' => ['type' => 'INT', 'null' => true]
        ];

        $this->forge->addColumn('sys_wfactivity', $field);
        $this->forge->addColumn('sys_wfevent_audit', $field);
    }

    public function down()
    {
        $field = ['tableline', 'recordline_id'];

        $this->forge->dropColumn('sys_wfactivity', $field);
        $this->forge->dropColumn('sys_wfevent_audit', $field);
    }
}
