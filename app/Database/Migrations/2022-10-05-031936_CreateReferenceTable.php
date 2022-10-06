<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReferenceTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'sys_reference_id'      => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'name'                  => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => false],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'validationtype'        => ['type' => 'CHAR', 'constraint' => 1, 'null' => true]
        ]);
        $this->forge->addKey('sys_reference_id', true);
        $this->forge->createTable('sys_reference', true);
    }

    public function down()
    {
        $this->forge->dropTable('sys_reference', true);
    }
}
