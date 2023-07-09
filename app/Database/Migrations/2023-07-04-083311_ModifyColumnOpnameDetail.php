<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyColumnOpnameDetail extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'branch'            => [
                'name'          => 'isbranch',
                'type'          => 'CHAR',
                'constraint'    => 1,
                'null'          => false,
                'default'       => 'N'
            ],
            'room'              => [
                'name'          => 'isroom',
                'type'          => 'CHAR',
                'constraint'    => 1,
                'null'          => false,
                'default'       => 'N'
            ],
            'employee'          => [
                'name'          => 'isemployee',
                'type'          => 'CHAR',
                'constraint'    => 1,
                'null'          => false,
                'default'       => 'N'
            ],
            'ischeck'           => [
                'name'          => 'isnew',
                'type'          => 'CHAR',
                'constraint'    => 1,
                'null'          => false,
                'default'       => 'N'
            ]
        ];

        $this->forge->modifyColumn('trx_opname_detail', $fields);

        $fields2 = [
            'nocheck'           => [
                'type'          => 'DECIMAL',
                'after'         => 'isnew',
                'constraint'    => 10,
                'null'          => false,
                'default'       => 0
            ]
        ];

        $this->forge->addColumn('trx_opname_detail', $fields2);
    }

    public function down()
    {
        $fields = ['nocheck'];

        $this->forge->dropColumn('trx_opname_detail', $fields);
    }
}
