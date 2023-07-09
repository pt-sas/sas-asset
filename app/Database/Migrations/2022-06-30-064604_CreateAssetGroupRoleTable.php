<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssetGroupRoleTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'sys_asset_role_id'     => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'sys_role_id'           => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'md_groupasset_id'      => ['type' => 'INT', 'constraint' => 6, 'null' => false],
        ]);
        $this->forge->addKey('sys_asset_role_id', true);
        $this->forge->createTable('sys_asset_role', true);
    }

    public function down()
    {
        $this->forge->dropTable('sys_asset_role', true);
    }
}
