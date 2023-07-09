<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccessMenuTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'sys_access_menu_id'    => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'sys_role_id'           => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'sys_menu_id'           => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'sys_submenu_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'isview'                => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'N'],
            'iscreate'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'N'],
            'isupdate'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'N'],
            'isdelete'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'N']
        ]);
        $this->forge->addKey('sys_access_menu_id', true);
        $this->forge->createTable('sys_access_menu', true);
    }

    public function down()
    {
        $this->forge->dropTable('sys_access_menu', true);
    }
}
