<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyColumnMenuAction extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'action'            => [
                'type'          => 'CHAR',
                'after'         => 'icon',
                'constraint'    => 1,
                'null'          => false
            ]
        ];

        $this->forge->modifyColumn('sys_menu', $fields);

        $fields2 = [
            'action'            => [
                'type'          => 'CHAR',
                'after'         => 'icon',
                'constraint'    => 1,
                'null'          => false
            ]
        ];

        $this->forge->modifyColumn('sys_submenu', $fields);
    }

    public function down()
    {
        $fields = [
            'action'            => [
                'type'          => 'INT',
                'after'         => 'icon',
                'constraint'    => 6,
                'null'          => false
            ]
        ];

        $this->forge->modifyColumn('sys_menu', $fields);

        $fields2 = [
            'action'            => [
                'type'          => 'INT',
                'after'         => 'icon',
                'constraint'    => 6,
                'null'          => false
            ]
        ];

        $this->forge->modifyColumn('sys_submenu', $fields2);
    }
}
