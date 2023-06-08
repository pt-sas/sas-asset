<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnDescriptionTrxInventoryTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'description'       => [
                'type'          => 'VARCHAR',
                'after'         => 'md_status_id',
                'constraint'    => 255,
                'null'          => false
            ]
        ];

        $this->forge->addColumn('trx_inventory', $fields);
    }

    public function down()
    {
        $fields = ['description'];

        $this->forge->dropColumn('trx_inventory', $fields);
    }
}
