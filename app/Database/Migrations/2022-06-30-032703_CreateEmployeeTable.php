<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmployeeTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'md_employee_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'value'                 => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => false],
            'name'                  => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => false],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'md_branch_id'          => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'md_division_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'md_room_id'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'sys_user_id'           => ['type' => 'INT', 'constraint' => 6, 'null' => false]
        ]);
        $this->forge->addKey('md_employee_id', true);
        $this->forge->createTable('md_employee', true);
    }

    public function down()
    {
        $this->forge->dropTable('md_employee', true);
    }
}
