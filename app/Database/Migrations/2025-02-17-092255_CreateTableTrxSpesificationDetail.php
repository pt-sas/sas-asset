<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableTrxSpesificationDetail extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'trx_spesification_detail_id'  => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'trx_spesification_id'  => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'necessary'             => ['type' => 'VARCHAR', 'constraint' => 16, 'null' => false],
            'lineno'                => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'md_sparepart_id'       => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false]
        ]);

        $this->forge->addKey('trx_spesification_detail_id', true);
        $this->forge->createTable('trx_spesification_detail', true);
    }

    public function down()
    {
        $this->forge->dropTable('trx_spesification_detail', true);
    }
}
