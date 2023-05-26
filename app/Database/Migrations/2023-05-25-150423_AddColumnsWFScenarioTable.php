<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsWFScenarioTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'md_branch_id'      => [
                'type'          => 'INT',
                'after'         => 'md_status_id',
                'constraint'    => 6,
                'null'          => false
            ],
            'md_division_id'    => [
                'type'          => 'INT',
                'after'         => 'md_branch_id',
                'constraint'    => 6,
                'null'          => false
            ]
        ];

        $this->forge->addColumn('sys_wfscenario', $fields);
    }

    public function down()
    {
        $fields = ['md_branch_id', 'md_division_id'];

        $this->forge->dropColumn('sys_wfscenario', $fields);
    }
}
