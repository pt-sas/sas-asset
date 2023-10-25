<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnScenarioType extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'scenariotype'       => [
                'type'          => 'VARCHAR',
                'constraint'    => 20,
                'null'          => true
            ]
        ];

        $this->forge->addColumn('sys_wfscenario', $fields);
    }

    public function down()
    {
        $fields = ['scenariotype'];
        $this->forge->dropColumn('sys_wfscenario', $fields);
    }
}
