<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTrxOpnameDetailTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'trx_opname_detail_id'  => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'trx_opname_id'         => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'assetcode'             => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
            'md_product_id'         => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'isbranch'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'N'],
            'isroom'                => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'N'],
            'isemployee'            => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'N'],
            'isnew'                 => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'N'],
            'nocheck'               => ['type' => 'NUMERIC', 'constraint' => 10, 'null' => false]
        ]);
        $this->forge->addKey('trx_opname_detail_id', true);
        $this->forge->createTable('trx_opname_detail', true);
    }

    public function down()
    {
        $this->forge->dropTable('trx_opname_detail', true);
    }
}
