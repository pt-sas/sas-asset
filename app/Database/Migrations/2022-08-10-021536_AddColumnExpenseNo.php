<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnExpenseNo extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'expenseno'     => [
                'type'          => 'VARCHAR',
                'after'         => 'md_supplier_id',
                'constraint'    => 20,
                'null'          => false
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
