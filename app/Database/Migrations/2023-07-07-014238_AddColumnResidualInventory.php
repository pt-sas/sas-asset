<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnResidualInventory extends Migration
{
    public function up()
    {
        $fields = [
            'residualvalue'     => [
                'type'          => 'DOUBLE',
                'after'         => 'qtyentered',
                'null'          => false
            ]
        ];

        $this->forge->addColumn('trx_inventory', $fields);
    }

    public function down()
    {
        $fields = ['residualvalue'];
        $this->forge->dropColumn('trx_inventory', $fields);
    }
}
