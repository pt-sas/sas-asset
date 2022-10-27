<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ReferenceFromList extends Seeder
{
    public function run()
    {
        $reference = [
            'created_by'    => 1,
            'updated_by'    => 1,
            'name'          => 'FromList',
            'description'   => 'From list',
            'validationtype' => 'L'
        ];

        $this->db->table('sys_reference')->insert($reference);

        $ref_list = [
            [
                'created_by'    => 1,
                'updated_by'    => 1,
                'value'         => 'E',
                'name'          => 'Employee',
                'isactive'      => 'Y',
                'sys_reference_id' => $this->db->insertID()
            ],
            [
                'created_by'    => 1,
                'updated_by'    => 1,
                'value'         => 'S',
                'name'          => 'Supplier',
                'isactive'      => 'Y',
                'sys_reference_id' => $this->db->insertID()
            ],
            [
                'created_by'    => 1,
                'updated_by'    => 1,
                'value'         => 'O',
                'name'          => 'Other',
                'isactive'      => 'Y',
                'sys_reference_id' => $this->db->insertID()
            ]
        ];

        $this->db->table('sys_ref_detail')->insertBatch($ref_list);
    }
}
