<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnStartDate extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'startdate'         => [
                'type'          => 'TIMESTAMP',
                'after'         => 'docstatus',
                'null'          => true
            ],
            'enddate'           => [
                'type'          => 'TIMESTAMP',
                'after'         => 'startdate',
                'null'          => true
            ]
        ];

        $this->forge->addColumn('trx_opname', $fields);
    }

    public function down()
    {
        $fields = ['startdate', 'enddate'];

        $this->forge->dropColumn('trx_opname', $fields);
    }
}
