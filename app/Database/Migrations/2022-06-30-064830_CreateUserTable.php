<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'sys_user_id'           => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'username'              => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => false],
            'name'                  => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => false],
            'password'              => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => false],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'email'                 => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
            'isnopasswordreset'     => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'N'],
            'datelastlogin'         => ['type' => 'timestamp', 'null' => true],
            'datepasswordchange'    => ['type' => 'timestamp', 'null' => true],
        ]);
        $this->forge->addKey('sys_user_id', true);
        $this->forge->createTable('sys_user', true);
    }

    public function down()
    {
        $this->forge->dropTable('sys_user', true);
    }
}
