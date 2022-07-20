<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSupplierTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'md_supplier_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'value'                 => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => false],
            'name'                  => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => false],
            'email'                 => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'address'               => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'phone'                 => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'isvendor'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false],
            'isservice'             => ['type' => 'CHAR', 'constraint' => 1, 'null' => false],
            'owner'                 => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false]
        ]);
        $this->forge->addKey('md_supplier_id', true);
        $this->forge->createTable('md_supplier', true);
    }

    public function down()
    {
        $this->forge->dropTable('md_supplier', true);
    }
}
