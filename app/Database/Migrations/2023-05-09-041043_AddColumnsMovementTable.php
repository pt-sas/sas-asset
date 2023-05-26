<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsMovementTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'movementtype'      => [
                'type'          => 'VARCHAR',
                'after'         => 'movementdate',
                'constraint'    => 20,
                'null'          => false
            ],
            'md_branch_id'      => [
                'type'          => 'INT',
                'after'         => 'docstatus',
                'constraint'    => 6,
                'null'          => false
            ],
            'md_division_id'    => [
                'type'          => 'INT',
                'after'         => 'md_branch_id',
                'constraint'    => 6,
                'null'          => false
            ],
            'sys_wfscenario_id' => [
                'type'          => 'INT',
                'after'         => 'md_status_id',
                'constraint'    => 6,
                'null'          => false
            ]
        ];

        $this->forge->addColumn('trx_movement', $fields);
    }

    public function down()
    {
        $fields = ['movementtype', 'md_branch_id', 'md_division_id', 'sys_wfscenario_id'];

        $this->forge->dropColumn('trx_movement', $fields);
    }
}
