<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssetDetailTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'md_asset_detail_id'    => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'assetcode'             => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
            'md_employee_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'processor'             => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => false],
            'motherboard'           => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => false],
            'vgacard'               => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => false],
            'vgatype'               => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => false],
            'ethernetcontroller'    => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => false],
            'macaddress'            => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => false],
            'ipaddress'             => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => false],
            'harddisk'              => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => false],
            'md_brand_id'           => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'powersupply'           => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
            'operatingsystem'       => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
        ]);
        $this->forge->addKey('md_asset_detail_id', true);
        $this->forge->createTable('md_asset_detail', true);
    }

    public function down()
    {
        $this->forge->dropTable('md_asset_detail', true);
    }
}
