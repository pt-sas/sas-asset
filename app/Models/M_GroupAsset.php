<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_GroupAsset extends Model
{
    protected $table      = 'md_groupasset';
    protected $primaryKey = 'md_groupasset_id';
    protected $allowedFields = [
        'value',
        'name',
        'description',
        'initialcode',
        'usefullife',
        'isactive',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\Brand';
    protected $column_order = [
        '', // Hide column
        '', // Number column
        'md_groupasset.value',
        'md_groupasset.name',
        'md_groupasset.description',
        'md_groupasset.initialcode',
        'md_groupasset.usefullife',
        'md_groupasset.isactive'
    ];
    protected $column_search = [
        'md_groupasset.value',
        'md_groupasset.name',
        'md_groupasset.description',
        'md_groupasset.initialcode',
        'md_groupasset.usefullife',
        'md_groupasset.isactive'
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
}
