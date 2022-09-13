<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChangeLogTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'sys_changelog_id'      => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'sys_sessions_id'       => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'table'                 => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => false],
            'column'                => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => false],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'record_id'             => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'oldvalue'              => ['type' => 'VARCHAR', 'constraint' => 2000, 'null' => true],
            'newvalue'              => ['type' => 'VARCHAR', 'constraint' => 2000, 'null' => true],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'eventchangelog'        => ['type' => 'CHAR', 'constraint' => 1, 'null' => true],
        ]);
        $this->forge->addKey('sys_changelog_id', true);
        $this->forge->createTable('sys_changelog', true);
    }

    public function down()
    {
        $this->forge->dropTable('sys_changelog', true);
    }
}
