<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoryTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'md_category_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'value'                 => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => false],
            'name'                  => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => false],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'initialcode'           => ['type' => 'CHAR', 'constraint' => 6, 'null' => false],
            'md_assetgroup_id'      => ['type' => 'INT', 'constraint' => 6, 'null' => false]
        ]);
        $this->forge->addKey('md_category_id', true);
        $this->forge->createTable('md_category', true);
    }

    public function down()
    {
        $this->forge->dropTable('md_category', true);
    }
}
