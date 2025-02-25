<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnFlaggingSparePartForComponentMasterProduct extends Migration
{
    public function up()
    {
        $fields = ['ismasterpart' => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y']];

        $this->forge->addColumn('md_category', $fields);
        $this->forge->addColumn('md_subcategory', $fields);
        $this->forge->addColumn('md_type', $fields);
        $this->forge->addColumn('md_variant', $fields);
    }

    public function down()
    {
        $fields = ['ismasterpart'];

        $this->forge->dropColumn('md_category', $fields);
        $this->forge->dropColumn('md_subcategory', $fields);
        $this->forge->dropColumn('md_type', $fields);
        $this->forge->dropColumn('md_variant', $fields);
    }
}
