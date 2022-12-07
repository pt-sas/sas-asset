<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnGroupAssetID extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $fields = [
            'md_groupasset_id'  => [
                'type'          => 'INT',
                'after'         => 'inventorydate',
                'constraint'    => 6,
                'null'          => false
            ]
        ];

        $this->forge->addColumn('trx_inventory', $fields);

        $fieldGroup = [
            'depreciationtype'  => [
                'type'          => 'CHAR',
                'after'         => 'md_sequence_id',
                'constraint'    => 2,
                'null'          => false
            ]
        ];

        $this->forge->addColumn('md_groupasset', $fieldGroup);
    }

    public function down()
    {
        $fields = ['md_groupasset_id'];

        $this->forge->dropColumn('trx_inventory', $fields);

        $fieldGroup = ['depreciationtype'];
        $this->forge->dropColumn('md_groupasset', $fieldGroup);
    }
}
