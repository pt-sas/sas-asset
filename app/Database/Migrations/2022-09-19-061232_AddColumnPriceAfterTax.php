<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnPriceAfterTax extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'istax'             => [
                'type'          => 'CHAR',
                'after'         => 'unitprice',
                'null'          => false,
                'default'       => 'N'
            ],
            'priceaftertax'     => [
                'type'          => 'DOUBLE',
                'after'         => 'istax',
                'null'          => false
            ]
        ];

        $this->forge->addColumn('trx_receipt_detail', $fields);
    }

    public function down()
    {
        $fields = ['istax', 'priceaftertax'];

        $this->forge->dropColumn('trx_receipt_detail', $fields);
    }
}
