<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnIsInternalUse extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'isinternaluse'     => [
                'type'          => 'CHAR',
                'after'         => 'md_supplier_id',
                'constraint'    => 1,
                'null'          => false,
                'default'       => 'N'
            ]
        ];

        $this->forge->addColumn('trx_quotation', $fields);
    }

    public function down()
    {
        $fields = ['isinternaluse'];

        $this->forge->dropColumn('trx_quotation', $fields);
    }
}
