<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnGroupAssetWfScenario extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'md_groupasset_id'  => [
                'type'          => 'INT',
                'after'         => 'menu',
                'constraint'    => 6,
                'null'          => false
            ]
        ];

        $this->forge->addColumn('sys_wfscenario', $fields);
    }

    public function down()
    {
        $fields = ['md_groupasset_id'];

        $this->forge->dropColumn('sys_wfscenario', $fields);
    }
}
