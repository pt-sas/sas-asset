<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWScenarioDetailTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'sys_wfscenario_detail_id'     => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'grandtotal'            => ['type' => 'NUMERIC', 'null' => true],
            'lineno'                => ['type' => 'NUMERIC', 'null' => true],
            'sys_wfscenario_id'     => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'sys_wfresponsible_id'  => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'default' => NULL],
            'sys_notiftext_id'      => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'default' => NULL],
        ]);
        $this->forge->addKey('sys_wfscenario_detail_id', true);
        $this->forge->createTable('sys_wfscenario_detail', true);
    }

    public function down()
    {
        $this->forge->dropTable('sys_wfscenario_detail', true);
    }
}
