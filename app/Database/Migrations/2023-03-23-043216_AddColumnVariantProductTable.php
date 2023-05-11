<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnVariantProductTable extends Migration
{
    public function up()
    {
        $fields = [
            'md_variant_id'             => [
                'type'          => 'INT',
                'after'         => 'md_sequence_id',
                'constraint'    => 6,
                'null'          => false
            ]
        ];

        $this->forge->addColumn('md_product', $fields);
    }

    public function down()
    {
        $fields = ['md_variant_id'];

        $this->forge->dropColumn('md_product', $fields);
    }
}
