<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnScenarioAndGroupAssetTrxQuotationTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'md_groupasset_id'  => [
                'type'          => 'INT',
                'after'         => 'isfrom',
                'constraint'    => 6,
                'null'          => false
            ],
            'sys_wfscenario_id' => [
                'type'          => 'INT',
                'after'         => 'md_groupasset_id',
                'constraint'    => 6,
                'null'          => false
            ]
        ];

        $this->forge->addColumn('trx_quotation', $fields);
    }

    public function down()
    {
        $fields = ['md_groupasset_id, sys_wfscenario_id'];

        $this->forge->dropColumn('trx_quotation', $fields);
    }
}
