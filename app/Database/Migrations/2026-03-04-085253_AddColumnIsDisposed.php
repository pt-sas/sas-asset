<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnIsDisposed extends Migration
{
    public function up()
    {
        $fields = [
            'isdisposed' => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'N']
        ];

        $this->forge->addColumn('trx_inventory', $fields);
    }

    public function down()
    {
        $fields = ['isdisposed'];

        $this->forge->dropColumn('trx_inventory', $fields);
    }
}
