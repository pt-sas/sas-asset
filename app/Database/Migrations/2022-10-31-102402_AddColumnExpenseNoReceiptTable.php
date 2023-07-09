<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnExpenseNoReceiptTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'expenseno'     => [
                'type'          => 'VARCHAR',
                'after'         => 'isinternaluse',
                'constraint'    => 20,
                'null'          => true
            ]
        ];

        $this->forge->addColumn('trx_receipt', $fields);
    }

    public function down()
    {
        $fields = ['expenseno'];

        $this->forge->dropColumn('trx_receipt', $fields);
    }
}
