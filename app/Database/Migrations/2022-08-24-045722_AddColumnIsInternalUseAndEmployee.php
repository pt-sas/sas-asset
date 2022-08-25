<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnIsInternalUseAndEmployee extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'md_employee_id'    => [
                'type'          => 'INT',
                'after'         => 'md_supplier_id',
                'constraint'    => 6,
                'null'          => true
            ]
        ];

        $this->forge->addColumn('trx_quotation', $fields);

        $fields2 = [
            'md_employee_id'    => [
                'type'          => 'INT',
                'after'         => 'md_supplier_id',
                'constraint'    => 6,
                'null'          => true
            ],
            'isinternaluse'     => [
                'type'          => 'CHAR',
                'after'         => 'expenseno',
                'contraint'     => 1,
                'null'          => false,
                'default'       => 'N'
            ]
        ];

        $this->forge->addColumn('trx_receipt', $fields2);
    }

    public function down()
    {
        $fields = ['md_employee_id'];

        $this->forge->dropColumn('trx_receipt', $fields);

        $fields2 = ['md_employee_id', 'isinternaluse'];

        $this->forge->dropColumn('trx_quotation', $fields2);
    }
}
