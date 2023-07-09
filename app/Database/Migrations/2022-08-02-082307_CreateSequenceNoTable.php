<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSequenceNoTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'md_sequence_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'calendaryearmonth'     => ['type' => 'VARCHAR', 'constraint' => 6, 'null' => false, 'default' => '0000'],
            'md_groupasset_id'      => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'categorycode'          => ['type' => 'VARCHAR', 'constraint' => 4, 'null' => false, 'default' => ''],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'currentnext'           => ['type' => 'NUMERIC', 'constraint' => 10, 'null' => false],
            'maxvalue'              => ['type' => 'NUMERIC', 'constraint' => 10, 'null' => false, 'default' => 0],
        ]);
        $this->forge->addKey(['md_sequence_id', 'calendaryearmonth', 'md_groupasset_id', 'categorycode'], true);
        $this->forge->addForeignKey('md_sequence_id', 'md_sequence', 'md_sequence_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('md_sequence_no', true);
    }

    public function down()
    {
        $this->forge->dropTable('md_sequence_no', true);
    }
}
