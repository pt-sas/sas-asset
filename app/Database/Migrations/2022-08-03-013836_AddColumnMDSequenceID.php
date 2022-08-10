<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnMDSequenceID extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'md_sequence_id'  => [
                'type'          => 'INT',
                'after'         => 'usefullife',
                'constraint'    => 6,
                'null'          => false
            ]
        ];

        $this->forge->addColumn('md_groupasset', $fields);
    }

    public function down()
    {
        $fields = ['md_sequence_id'];

        $this->forge->dropColumn('md_groupasset', $fields);
    }
}
