<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AddRoleForViewData extends Seeder
{
    public function run()
    {
        $data = [
            [
                'created_by'    => 1,
                'updated_by'    => 1,
                'name'          => 'W_Move_All_Data',
                'description'   => 'Role for move all data',
                'ismanual'      => 'Y',
                'iscanexport'   => 'N',
                'iscanreport'   => 'N',
                'isallowmultipleprint' => 'N',
            ],
            [
                'created_by'    => 1,
                'updated_by'    => 1,
                'name'          => 'W_View_All_Mgr_Data',
                'description'   => 'Role for view all data for manager',
                'ismanual'      => 'Y',
                'iscanexport'   => 'N',
                'iscanreport'   => 'N',
                'isallowmultipleprint' => 'N',
            ]
        ];

        $this->db->table('sys_role')->insertBatch($data);
    }
}
