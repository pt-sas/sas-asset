<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnIsAgreeOnTrxServiceDetail extends Migration
{
    public function up()
    {
        $fields = [
            'isagree'        => ['type' => 'VARCHAR', 'constraint' => 1, 'null' => true, 'default' => null]
        ];

        $this->forge->addColumn('trx_service_detail', $fields);
    }

    public function down()
    {
        $fields = [
            'isagree'
        ];

        $this->forge->dropColumn('trx_service_detail', $fields);
    }
}
