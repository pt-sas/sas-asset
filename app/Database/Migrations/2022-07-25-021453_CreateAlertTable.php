<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAlertTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'md_alertrecipient_id'  => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'record_id'             => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'sys_user_id'           => ['type' => 'INT', 'constraint' => 6, 'null' => true],
            'sys_role_id'           => ['type' => 'INT', 'constraint' => 6, 'null' => true],
        ]);
        $this->forge->addKey('md_alertrecipient_id', true);
        $this->forge->createTable('md_alertrecipient', true);
    }

    public function down()
    {
        $this->forge->dropTable('md_alertrecipient', true);
    }
}
