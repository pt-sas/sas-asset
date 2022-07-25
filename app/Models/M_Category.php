<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_Category extends Model
{
    protected $table      = 'md_category';
    protected $primaryKey = 'md_category_id';
    protected $allowedFields = [
        'value',
        'name',
        'initialcode',
        'md_groupasset_id',
        'isactive',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\Category';
    protected $column_order = [
        '', // Hide column
        '', // Number column
        'md_category.value',
        'md_category.name',
        'md_category.initialcode',
        'md_category.isactive'
    ];
    protected $column_search = [
        'md_category.value',
        'md_category.name',
        'md_category.initialcode',
        'md_category.isactive'
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

    public function getSeqNumber()
    {
        $number = $this->builder->countAll();

        $number += 1;

        while (strlen($number) < 5) {
            $number = "0" . $number;
        }

        // Check exist value number
        $row = $this->builder->where("MID(value, 3, 5)", $number)->get()->getRow();

        if ($row) {
            $number += 1;

            while (strlen($number) < 5) {
                $number = "0" . $number;
            }
        }

        return $number;
    }
}
