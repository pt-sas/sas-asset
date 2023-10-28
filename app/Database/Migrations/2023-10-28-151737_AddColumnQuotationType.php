<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnQuotationType extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'quotationtype'     => [
                'type'          => 'VARCHAR',
                'after'         => 'isfrom',
                'constraint'    => 20,
                'null'          => true
            ]
        ];

        $this->forge->addColumn('trx_quotation', $fields);
    }

    public function down()
    {
        $fields = ['quotationtype'];
        $this->forge->dropColumn('trx_quotation', $fields);
    }
}
