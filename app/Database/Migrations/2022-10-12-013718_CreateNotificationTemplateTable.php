<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationTemplateTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'sys_notiftext_id'      => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'name'                  => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => false],
            'subject'               => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'text'                  => ['type' => 'VARCHAR', 'constraint' => 2000, 'null' => false],
            'text2'                 => ['type' => 'VARCHAR', 'constraint' => 2000, 'null' => true],
            'text3'                 => ['type' => 'VARCHAR', 'constraint' => 2000, 'null' => true],
            'notiftype'             => ['type' => 'CHAR', 'constraint' => 1, 'null' => false]
        ]);

        $this->forge->addKey('sys_notiftext_id', true);
        $this->forge->createTable('sys_notiftext', true);
    }

    public function down()
    {
        $this->forge->dropTable('sys_notiftext', true);
    }
}
