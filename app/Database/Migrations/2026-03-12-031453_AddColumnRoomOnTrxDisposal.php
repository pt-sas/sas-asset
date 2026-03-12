<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnRoomOnTrxDisposal extends Migration
{
    public function up()
    {
        $fields = ['md_room_id' => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'after' => 'disposaltype']];

        $this->forge->addColumn('trx_disposal', $fields);
    }

    public function down()
    {
        $fields = ['md_room_id'];

        $this->forge->dropColumn('trx_disposal', $fields);
    }
}
