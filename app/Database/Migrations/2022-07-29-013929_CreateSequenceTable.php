<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSequenceTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'md_sequence_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'name'                  => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'vformat'               => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'isautosequence'        => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'incrementno'           => ['type' => 'NUMERIC', 'constraint' => 10, 'null' => false],
            'startno'               => ['type' => 'NUMERIC', 'constraint' => 10, 'null' => false],
            'currentnext'           => ['type' => 'NUMERIC', 'constraint' => 10, 'null' => false],
            'prefix'                => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'suffix'                => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'startnewyear'          => ['type' => 'CHAR', 'constraint' => 1, 'null' => true, 'default' => 'N'],
            'datecolumn'            => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'decimalpattern'        => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'startnewmonth'         => ['type' => 'CHAR', 'constraint' => 1, 'null' => true, 'default' => 'N'],
            'isgassetlevelsequence' => ['type' => 'CHAR', 'constraint' => 1, 'null' => true, 'default' => 'N'],
            'gassetcolumn'          => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'iscategorylevelsequence' => ['type' => 'CHAR', 'constraint' => 1, 'null' => true, 'default' => 'N'],
            'categorycolumn'        => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'maxvalue'              => ['type' => 'NUMERIC', 'null' => false, 'default' => 0],
        ]);
        $this->forge->addKey('md_sequence_id', true);
        $this->forge->createTable('md_sequence', true);
    }

    public function down()
    {
        $this->forge->dropTable('md_sequence', true);
    }
}
