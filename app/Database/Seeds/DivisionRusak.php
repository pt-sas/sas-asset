<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DivisionRusak extends Seeder
{
    public function run()
    {
        $data = [
            [
                'created_by'    => 1,
                'updated_by'    => 1,
                'value'         => 'DV00023',
                'name'          => 'IT-RUSAK'
            ],
            [
                'created_by'    => 1,
                'updated_by'    => 1,
                'value'         => 'DV00024',
                'name'          => 'HRD-RUSAK'
            ]
        ];

        $this->db->table('md_division')->insertBatch($data);
    }
}