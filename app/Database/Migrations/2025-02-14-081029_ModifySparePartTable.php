<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifySparePartTable extends Migration
{
    public function up()
    {
        $field = [
            'product_category_id'   => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'after' => 'name'],
            'md_brand_id'           => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'after' => 'product_category_id'],
            'md_category_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'after' => 'md_brand_id'],
            'md_subcategory_id'     => ['type' => 'INT', 'constraint' => 6, 'null' => true, 'after' => 'md_category_id'],
            'md_type_id'            => ['type' => 'INT', 'constraint' => 6, 'null' => true, 'after' => 'md_subcategory_id'],
            'md_variant_id'         => ['type' => 'INT', 'constraint' => 6, 'null' => true, 'after' => 'md_type_id'],
        ];

        $this->forge->addColumn('md_sparepart', $field);

        $dropField = ['md_product_id'];

        $this->forge->dropColumn('md_sparepart', $dropField);
    }

    public function down()
    {
        $dropField = [
            'product_category_id',
            'md_brand_id',
            'md_category_id',
            'md_subcategory_id',
            'md_type_id',
            'md_variant_id'
        ];

        $this->forge->dropColumn('md_sparepart', $dropField);

        $field = ['md_product_id'         => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'after' => 'name']];

        $this->forge->addColumn('md_sparepart', $field);
    }
}
