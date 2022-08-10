<?php

namespace App\Models;

use CodeIgniter\Model;

use CodeIgniter\HTTP\RequestInterface;

class M_Product extends Model
{
    protected $table      = 'md_product';
    protected $primaryKey = 'md_product_id';
    protected $allowedFields = [
        'value',
        'name',
        'description',
        'md_brand_id',
        'md_category_id',
        'md_subcategory_id',
        'md_type_id',
        'isactive',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\Product';
    protected $column_order = [
        '', // Hide column
        '', // Number column
        'md_product.value',
        'md_product.name',
        'md_brand.name',
        'md_category.name',
        'md_subcategory.name',
        'md_type.name',
        'md_product.description',
        'md_product.isactive'
    ];
    protected $column_search = [
        'md_product.value',
        'md_product.name',
        'md_brand.name',
        'md_category.name',
        'md_subcategory.name',
        'md_type.name',
        'md_product.description',
        'md_product.isactive'
    ];
    protected $order = ['value' => 'ASC'];
    protected $request;
    protected $db;
    protected $builder;

    public function __construct(RequestInterface $request)
    {
        parent::__construct();
        $this->db = db_connect();
        $this->request = $request;
        $this->builder = $this->db->table($this->table);
    }

    public function getSelect()
    {
        $sql = $this->table . '.*,' .
            'md_brand.name as brand,
                md_category.name as category,
                md_subcategory.name as subcategory,
                md_type.name as type';

        return $sql;
    }

    public function getJoin()
    {
        $sql = [
            $this->setDataJoin('md_brand', 'md_brand.md_brand_id = ' . $this->table . '.md_brand_id', 'left'),
            $this->setDataJoin('md_category', 'md_category.md_category_id = ' . $this->table . '.md_category_id', 'left'),
            $this->setDataJoin('md_subcategory', 'md_subcategory.md_subcategory_id = ' . $this->table . '.md_subcategory_id', 'left'),
            $this->setDataJoin('md_type', 'md_type.md_type_id = ' . $this->table . '.md_type_id', 'left'),
        ];

        return $sql;
    }

    private function setDataJoin($tableJoin, $columnJoin, $typeJoin = "inner")
    {
        return [
            "tableJoin" => $tableJoin,
            "columnJoin" => $columnJoin,
            "typeJoin" => $typeJoin
        ];
    }

    public function getProductAsset($id)
    {
        $this->builder->select($this->table . '.md_product_id,' .
            'md_category.md_category_id,
            md_category.initialcode as category_code,
            md_groupasset.md_groupasset_id,
            md_groupasset.initialcode as groupasset_code,
            md_sequence.*');

        $this->builder->join('md_category', 'md_category.md_category_id = ' . $this->table . '.md_category_id', 'left');
        $this->builder->join('md_groupasset', 'md_groupasset.md_groupasset_id = md_category.md_groupasset_id', 'left');
        $this->builder->join('md_sequence', 'md_sequence.md_sequence_id = md_groupasset.md_sequence_id', 'left');

        // if (count($id) > 0)
        $this->builder->where($this->table . '.md_product_id', $id);

        return $this->builder->get();
    }
}
