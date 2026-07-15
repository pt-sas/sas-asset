<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableAccountingForGroupAsset extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'md_account_id' => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'      => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'    => ['type' => 'timestamp default current_timestamp'],
            'created_by'    => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'    => ['type' => 'timestamp default current_timestamp'],
            'updated_by'    => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'value'         => ['type' => 'VARCHAR', 'constraint' => 9, 'null' => false],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'account_sign'  => ['type' => 'CHAR', 'constraint' => 1, 'null' => false],
            'description'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true]
        ]);

        $this->forge->addKey('md_account_id', true);
        $this->forge->createTable('md_account', true);

        $this->forge->addField([
            'md_groupasset_acct_id' => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'md_groupasset_id'     => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'md_account_id'         => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'name'                  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'description'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true]
        ]);

        $this->forge->addKey('md_groupasset_acct_id', true);
        $this->forge->createTable('md_groupasset_acct', true);
    }

    public function down()
    {
        $this->forge->dropTable('md_account');
        $this->forge->dropTable('md_groupasset_acct');
    }
}
