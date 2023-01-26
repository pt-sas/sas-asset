<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnTableAlertRecipientTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'table'             => [
                'type'          => 'VARCHAR',
                'after'         => 'record_id',
                'constraint'    => 60,
                'null'          => false
            ]
        ];

        $this->forge->addColumn('md_alertrecipient', $fields);
    }

    public function down()
    {
        $fields = ['table'];

        $this->forge->dropColumn('md_alertrecipient', $fields);
    }
}
