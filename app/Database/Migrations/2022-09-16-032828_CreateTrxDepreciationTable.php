<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTrxDepreciationTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'trx_depreciation_id'   => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'assetcode'             => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
            'transactiondate'       => ['type' => 'timestamp', 'null' => false],
            'totalyear'             => ['type' => 'NUMERIC', 'constraint' => 5, 'null' => false],
            'startyear'             => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'residualvalue'         => ['type' => 'DOUBLE', 'null' => false],
            'costdepreciation'      => ['type' => 'DOUBLE', 'null' => false],
            'accumulateddepreciation' => ['type' => 'DOUBLE', 'null' => false],
            'bookvalue'             => ['type' => 'DOUBLE', 'null' => false],
            'currentmonth'          => ['type' => 'NUMERIC', 'constraint' => 5, 'null' => false],
            'depreciationtype'      => ['type' => 'CHAR', 'constraint' => 2, 'null' => false],
        ]);

        $this->forge->addKey('trx_depreciation_id', true);
        $this->forge->createTable('trx_depreciation', true);
    }

    public function down()
    {
        $this->forge->dropTable('trx_depreciation', true);
    }
}
