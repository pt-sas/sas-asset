<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTrxMovementDetailTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'trx_movement_detail_id' => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'trx_movement_id'       => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'assetcode'             => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => false],
            'md_product_id'         => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'employee_from'         => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'employee_to'           => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'division_from'         => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'division_to'           => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'branch_from'           => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'branch_to'             => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'room_from'             => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'room_to'               => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'md_status_id'          => ['type' => 'INT', 'constraint' => 6, 'null' => false],
        ]);
        $this->forge->addKey('trx_movement_detail_id', true);
        $this->forge->createTable('trx_movement_detail', true);
    }

    public function down()
    {
        $this->forge->dropTable('trx_movement_detail', true);
    }
}
