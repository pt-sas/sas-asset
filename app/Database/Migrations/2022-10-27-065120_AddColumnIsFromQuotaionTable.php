<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnIsFromQuotaionTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $field = [
            'isfrom'        => [
                'type'          => 'CHAR',
                'after'         => 'isinternaluse',
                'constraint'    => 1,
                'null'          => false
            ]
        ];

        $this->forge->addColumn('trx_quotation', $field);
    }

    public function down()
    {
        $field = ['isfrom'];

        $this->forge->dropColumn('trx_quotation', $field);
    }
}
