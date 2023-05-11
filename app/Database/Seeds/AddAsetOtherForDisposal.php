<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AddAsetOtherForDisposal extends Seeder
{
    public function run()
    {
        $data = [
            'created_by'    => 1,
            'updated_by'    => 1,
            'assetcode'     => 'OTHER',
            'inventorydate' => '2022-01-01 00:00:00',
            'isspare'       => 'Y',
        ];

        $this->db->table('trx_inventory')->insert($data);
    }
}
