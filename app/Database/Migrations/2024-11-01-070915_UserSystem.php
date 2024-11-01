<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserSystem extends Migration
{
    public function up()
    {
        $data = [
            'sys_user_id'   => 100000,
            'created_by'    => 1,
            'updated_by'    => 1,
            'username'      => 'System',
            'name'          => 'System',
            'password'      => '$2y$10$1.BPejUmjxi7Ljysw.KeEeG7bv0gj4z0xyy8w6uWfFLty.8pjqYSO',
        ];

        $this->db->table('sys_user')->insert($data);
    }

    public function down()
    {
        //
    }
}
