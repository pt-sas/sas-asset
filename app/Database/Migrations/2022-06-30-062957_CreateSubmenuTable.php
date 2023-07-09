<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubmenuTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'sys_submenu_id'           => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'name'                  => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => false],
            'url'                   => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => false],
            'sequence'              => ['type' => 'NUMERIC', 'constraint' => 5, 'null' => false],
            'icon'                  => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
            'action'                => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'initialcode'           => ['type' => 'CHAR', 'constraint' => 6, 'null' => false],
            'status'                => ['type' => 'CHAR', 'constraint' => 1, 'null' => false],
            'sys_menu_id'           => ['type' => 'INT', 'constraint' => 6, 'null' => false]
        ]);
        $this->forge->addKey('sys_submenu_id', true);
        $this->forge->createTable('sys_submenu', true);
    }

    public function down()
    {
        $this->forge->dropTable('sys_submenu', true);
    }
}
