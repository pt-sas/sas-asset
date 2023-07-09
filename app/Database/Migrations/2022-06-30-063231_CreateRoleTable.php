<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRoleTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'sys_role_id'           => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'name'                  => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => false],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'ismanual'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'iscanexport'           => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'N'],
            'iscanreport'           => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'N'],
            'isallowmultipleprint'  => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'N'],
        ]);
        $this->forge->addKey('sys_role_id', true);
        $this->forge->createTable('sys_role', true);
    }

    public function down()
    {
        $this->forge->dropTable('sys_role', true);
    }
}
