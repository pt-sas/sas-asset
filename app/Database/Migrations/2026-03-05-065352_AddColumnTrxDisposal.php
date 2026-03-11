<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnTrxDisposal extends Migration
{
    public function up()
    {
        $fields = [
            'bapno' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'bpkno' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sjkno' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ];

        $this->forge->addColumn('trx_disposal', $fields);
    }

    public function down()
    {
        $fields = ['bapno', 'bpkno', 'sjkno'];

        $this->forge->dropColumn('trx_disposal', $fields);
    }
}
