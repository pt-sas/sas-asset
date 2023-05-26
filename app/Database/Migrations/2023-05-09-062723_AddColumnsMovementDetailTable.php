<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsMovementDetailTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'isaccept'          => [
                'type'          => 'CHAR',
                'after'         => 'room_to',
                'constraint'    => 1,
                'null'          => false,
                'default'       => 'N'
            ]
        ];

        $this->forge->addColumn('trx_movement_detail', $fields);
    }

    public function down()
    {
        $fields = ['isaccept'];

        $this->forge->dropColumn('trx_movement_detail', $fields);
    }
}
