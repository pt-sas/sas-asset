<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyTableSparePart extends Migration
{
    public function up()
    {
        $fields = ['name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false]];

        $this->forge->modifyColumn('md_sparepart', $fields);
    }

    public function down()
    {
        $fields = ['name' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => false]];
        $this->forge->modifyColumn('md_sparepart', $fields);
    }
}