<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBarcodeTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'sys_barcode_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'barcodetype'           => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => false],
            'generatortype'         => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => false],
            'widthfactor'           => ['type' => 'NUMERIC', 'constraint' => 10, 'null' => false],
            'height'                => ['type' => 'NUMERIC', 'constraint' => 10, 'null' => false],
            'text'                  => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => false],
            'iswithtext'            => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'positiontext'          => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => false],
            'sizetext'              => ['type' => 'NUMERIC', 'constraint' => 10, 'null' => false],
        ]);

        $this->forge->addKey('sys_barcode_id', true);
        $this->forge->createTable('sys_barcode', true);
    }

    public function down()
    {
        $this->forge->dropTable('sys_barcode', true);
    }
}
