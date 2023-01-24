<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnInvoiceDate extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'invoicedate'       => [
                'type'          => 'TIMESTAMP',
                'after'         => 'receiptdate',
                'null'          => false
            ]
        ];

        $this->forge->addColumn('trx_receipt', $fields);
    }

    public function down()
    {
        $fields = ['invoicedate'];

        $this->forge->dropColumn('trx_receipt', $fields);
    }
}
