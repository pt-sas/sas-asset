<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'md_product_id'         => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'value'                 => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => false],
            'name'                  => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => false],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'specification'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'md_brand_id'           => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'md_category_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'md_subcategory_id'     => ['type' => 'INT', 'constraint' => 6, 'null' => true],
            'md_type_id'            => ['type' => 'INT', 'constraint' => 6, 'null' => true],
        ]);
        $this->forge->addKey('md_product_id', true);
        $this->forge->createTable('md_product', true);
    }

    public function down()
    {
        $this->forge->dropTable('md_product', true);
    }
}
