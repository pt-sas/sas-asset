<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySpesificationComputer extends Seeder
{
    public function run()
    {
        $category = [
            [
                'md_category_id' => 100140,
                'created_by' => 1,
                'updated_by' => 1,
                'value' => 'CT00135',
                'name'  => 'PROCESSOR',
                'md_groupasset_id' => 100007,
                'ismasterpart' => 'Y'
            ],
            [
                'md_category_id' => 100141,
                'created_by' => 1,
                'updated_by' => 1,
                'value' => 'CT00136',
                'name'  => 'MOTHERBOARD',
                'md_groupasset_id' => 100007,
                'ismasterpart' => 'Y'
            ],
            [
                'md_category_id' => 100142,
                'created_by' => 1,
                'updated_by' => 1,
                'value' => 'CT00137',
                'name'  => 'VGA CARD',
                'md_groupasset_id' => 100007,
                'ismasterpart' => 'Y'
            ],
            [
                'md_category_id' => 100143,
                'created_by' => 1,
                'updated_by' => 1,
                'value' => 'CT00138',
                'name'  => 'CASE',
                'md_groupasset_id' => 100007,
                'ismasterpart' => 'Y'
            ],
            [
                'md_category_id' => 100144,
                'created_by' => 1,
                'updated_by' => 1,
                'value' => 'CT00139',
                'name'  => 'POWER SUPPLY',
                'md_groupasset_id' => 100007,
                'ismasterpart' => 'Y'
            ],

            [
                'md_category_id' => 100145,
                'created_by' => 1,
                'updated_by' => 1,
                'value' => 'CT00140',
                'name'  => 'MEMORY',
                'md_groupasset_id' => 100007,
                'ismasterpart' => 'Y'
            ],
            [
                'md_category_id' => 100146,
                'created_by' => 1,
                'updated_by' => 1,
                'value' => 'CT00141',
                'name'  => 'STORAGE DATA',
                'md_groupasset_id' => 100007,
                'ismasterpart' => 'Y'
            ]
        ];

        $this->db->table('md_category')->insertBatch($category);
    }
}
