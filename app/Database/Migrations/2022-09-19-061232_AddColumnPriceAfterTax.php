<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnPriceAfterTax extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'priceaftertax'     => [
                'type'          => 'DOUBLE',
                'after'         => 'unitprice',
                'null'          => false
            ]
        ];

        $this->forge->addColumn('trx_receipt_detail', $fields);
    }

    public function down()
    {
        $fields = ['priceaftertax'];

        $this->forge->dropColumn('trx_receipt_detail', $fields);
    }
}
