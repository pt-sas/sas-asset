<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateServicePartTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'trx_service_part_id'   => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'trx_service_detail_id' => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'md_sparepart_id'       => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'qtyentered'            => ['type' => 'INT', 'constraint' => 10, 'null' => false],
            'unitprice'             => ['type' => 'DOUBLE', 'null' => false],
            'lineamt'               => ['type' => 'DOUBLE', 'null' => false],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false]
        ]);

        $this->forge->addKey('trx_service_part_id', true);
        $this->forge->createTable('trx_service_part', true);
    }

    public function down()
    {
        $this->forge->dropTable('trx_service_part', true);
    }
}
