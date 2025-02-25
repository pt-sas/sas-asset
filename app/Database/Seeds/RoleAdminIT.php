<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleAdminIT extends Seeder
{
    public function run()
    {
        $data = [
            [
                'created_by'    => 1,
                'updated_by'    => 1,
                'name'          => 'W_IT_Admin',
                'description'   => 'Role for Create Master Data Spare Part',
                'ismanual'      => 'Y',
                'iscanexport'   => 'N',
                'iscanreport'   => 'N',
                'isallowmultipleprint' => 'N',
            ],
        ];

        $this->db->table('sys_role')->insertBatch($data);
    }
}