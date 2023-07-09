<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnPICGroupAssetAndCategoryTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $field = [
            'pic'     => [
                'type'          => 'INT',
                'constraint'    => 6,
                'null'          => false
            ]
        ];

        $this->forge->addColumn('md_groupasset', $field);
        $this->forge->addColumn('md_category', $field);
    }

    public function down()
    {
        $field = ['pic'];

        $this->forge->dropColumn('md_groupasset', $field);
        $this->forge->dropColumn('md_category', $field);
    }
}
