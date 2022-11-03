<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMailTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'sys_email_id'          => ['type' => 'INT', 'constraint' => 6, 'null' => false, 'auto_increment' => true],
            'isactive'              => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'Y'],
            'created_at'            => ['type' => 'timestamp default current_timestamp'],
            'created_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'updated_at'            => ['type' => 'timestamp default current_timestamp'],
            'updated_by'            => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'protocol'              => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => false],
            'smtphost'              => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'smtpport'              => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'smtpcrypto'            => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'smtpuser'              => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'smtppassword'          => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'requestemail'          => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
        ]);

        $this->forge->addKey('sys_email_id', true);
        $this->forge->createTable('sys_email', true);
    }

    public function down()
    {
        $this->forge->dropTable('sys_email', true);
    }
}
