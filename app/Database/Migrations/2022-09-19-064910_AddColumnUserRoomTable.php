<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnUserRoomTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'userrep_id'       => [
                'type'          => 'INT',
                'after'         => 'md_branch_id',
                'constraint'    => 6,
                'null'          => false
            ]
        ];

        $this->forge->addColumn('md_room', $fields);
    }

    public function down()
    {
        $fields = ['userrep_id'];

        $this->forge->dropColumn('md_room', $fields);
    }
}
