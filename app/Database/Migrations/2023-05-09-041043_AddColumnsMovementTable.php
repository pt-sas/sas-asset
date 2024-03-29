<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsMovementTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'ref_movement_id'   => [
                'type'          => 'INT',
                'after'         => 'updated_by',
                'constraint'    => 6,
                'null'          => false
            ],
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
            'md_branchto_id'      => [
                'type'          => 'INT',
                'after'         => 'md_branch_id',
                'constraint'    => 6,
                'null'          => false
            ],
            'md_divisionto_id'  => [
                'type'          => 'INT',
                'after'         => 'md_branchto_id',
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
        $fields = ['movementtype', 'md_branch_id', 'md_division_id', 'md_branchto_id', 'md_divisionto_id', 'sys_wfscenario_id'];

        $this->forge->dropColumn('trx_movement', $fields);
    }
}
