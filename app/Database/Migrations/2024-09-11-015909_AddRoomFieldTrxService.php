<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoomFieldTrxService extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'md_room_id'        => [
                'type'          => 'INT',
                'constraint'    => 10,
                'null'          => false
            ]
        ];

        $this->forge->addColumn('trx_service', $fields);

        $modifyField = [
            'unitprice'         => [
                'name'          => 'lineamt',
                'type'          => 'DOUBLE',
                'null'          => false
            ]
        ];

        $this->forge->modifyColumn('trx_service_detail', $modifyField);
    }

    public function down()
    {
        $fields = ['md_room_id'];

        $this->forge->dropColumn('trx_service', $fields);
    }
}
