<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableTrxSpesification extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'trx_spesification_id'  => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'trx_inventory_id'      => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'processor_id'          => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'motherboard_id'        => ['type' => 'INT', 'constraint' => 6, 'null' => true],
            'video_graphic_id'      => ['type' => 'INT', 'constraint' => 6, 'null' => true],
            'diskdrive'             => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'N'],
            'case_id'               => ['type' => 'INT', 'constraint' => 6, 'null' => true],
            'power_supply_id'       => ['type' => 'INT', 'constraint' => 6, 'null' => true],
            'operation_id'          => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'ip_adress'             => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
            'mac_adress'            => ['type' => 'CHAR', 'constraint' => 17, 'null' => true],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false]
        ]);

        $this->forge->addKey('trx_spesification_id', true);
        $this->forge->createTable('trx_spesification', true);
    }

    public function down()
    {
        $this->forge->dropTable('trx_spesification', true);
    }
}
