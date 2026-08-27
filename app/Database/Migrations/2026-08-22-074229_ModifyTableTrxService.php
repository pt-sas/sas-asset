<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyTableTrxService extends Migration
{
    public function up()
    {
        $fields = [
            'isapproved'        => ['type' => 'ENUM', 'constraint' => ['Y', 'N'], 'null' => true, 'default' => null],
            'sys_wfscenario_id' => ['type' => 'INT', 'constraint' => 6, 'null' => true]
        ];

        $this->forge->addColumn('trx_service', $fields);
    }

    public function down()
    {
        $fields = [
            'isapproved',
            'sys_wfscenario_id'
        ];

        $this->forge->dropColumn('trx_service', $fields);
    }
}
