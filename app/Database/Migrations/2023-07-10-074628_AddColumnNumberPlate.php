<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnNumberPlate extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'numberplate'       => [
                'type'          => 'VARCHAR',
                'after'         => 'assetcode',
                'constraint'    => 20,
                'null'          => true
            ]
        ];

        $this->forge->addColumn('trx_inventory', $fields);

        $fields = [
            'numberplate'       => [
                'type'          => 'VARCHAR',
                'after'         => 'assetcode',
                'constraint'    => 20,
                'null'          => true
            ]
        ];

        $this->forge->addColumn('trx_receipt_detail', $fields);
    }

    public function down()
    {
        $fields = ['numberplate'];
        $this->forge->dropColumn('trx_inventory', $fields);
        $this->forge->dropColumn('trx_receipt_detail', $fields);
    }
}
