<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyColumnStatus extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'md_status_id'      => [
                'name'          => 'movementstatus',
                'type'          => 'INT',
                'after'         => 'description',
                'constraint'    => 6,
                'null'          => true
            ]
        ];

        $this->forge->modifyColumn('trx_movement', $fields);
    }

    public function down()
    {
        $fields = [
            'movementstatus'      => [
                'name'          => 'md_status_id',
                'type'          => 'INT',
                'after'         => 'description',
                'constraint'    => 6,
                'null'          => true
            ]
        ];

        $this->forge->modifyColumn('trx_movement', $fields);
    }
}
