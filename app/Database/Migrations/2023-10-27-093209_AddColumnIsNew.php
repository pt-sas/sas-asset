<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnIsNew extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'isnew'             => [
                'type'          => 'CHAR',
                'constraint'    => 1,
                'null'          => false,
                'default'       => 'N'
            ]
        ];

        $this->forge->addColumn('trx_inventory', $fields);
        $this->forge->addColumn('trx_movement_detail', $fields);
    }

    public function down()
    {
        $fields = ['isnew'];
        $this->forge->dropColumn('trx_inventory', $fields);
        $this->forge->dropColumn('trx_movement_detail', $fields);
    }
}
