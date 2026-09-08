<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnRepairCostOnService extends Migration
{
    public function up()
    {
        $fields = [
            'repair_cost' => ['type' => 'DOUBLE', 'null' => false]
        ];

        $this->forge->addColumn('trx_service_detail', $fields);
    }

    public function down()
    {
        $fields = ['repair_cost'];

        $this->forge->dropColumn('trx_service_detail', $fields);
    }
}
