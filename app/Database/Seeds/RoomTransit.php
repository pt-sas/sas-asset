<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoomTransit extends Seeder
{
    public function run()
    {
        $room = [
            'created_by'    => 1,
            'updated_by'    => 1,
            'value'         => 'RM00000',
            'name'          => 'TRANSIT'
        ];

        $this->db->table('md_room')->insert($room);
    }
}
