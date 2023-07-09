<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyColumnExpenseNo extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'expenseno'         => [
                'name'          => 'docreference',
                'type'          => 'VARCHAR',
                'after'         => 'md_supplier_id',
                'constraint'    => 20,
                'null'          => false
            ]
        ];

        $this->forge->modifyColumn('trx_receipt', $fields);

        $fields2 = [
            'docreference'     => [
                'type'          => 'VARCHAR',
                'after'         => 'md_supplier_id',
                'constraint'    => 20,
                'null'          => false
            ]
        ];

        $this->forge->addColumn('trx_quotation', $fields2);

        $fields3 = [
            'priceaftertax'     => [
                'name'          => 'residualvalue',
                'type'          => 'DOUBLE',
                'after'         => 'unitprice',
                'null'          => false
            ]
        ];

        $this->forge->modifyColumn('trx_receipt_detail', $fields3);
    }

    public function down()
    {
        $fields = [
            'docreference'      => [
                'name'          => 'expenseno',
                'type'          => 'VARCHAR',
                'after'         => 'md_supplier_id',
                'constraint'    => 20,
                'null'          => false
            ]
        ];

        $this->forge->modifyColumn('trx_receipt', $fields);

        $fields2 = ['docreference'];

        $this->forge->dropColumn('trx_quotation', $fields2);

        $fields3 = [
            'residualvalue'     => [
                'name'          => 'priceaftertax',
                'type'          => 'DOUBLE',
                'after'         => 'unitprice',
                'null'          => false
            ]
        ];

        $this->forge->modifyColumn('trx_receipt_detail', $fields3);
    }
}
