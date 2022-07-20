<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_Menu extends Model
{
    protected $table      = 'sys_menu';
    protected $primaryKey = 'sys_menu_id';
    protected $allowedFields = [
        'name',
        'sequence',
        'url',
        'icon',
        'status',
        'isactive',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\Menu';
    protected $column_order = [
        '', // Hide column
        '', // Number column
        'name',
        'url',
        'sequence',
        'icon',
        'isactive'
    ];
    protected $column_search = [
        'name',
        'url',
        'sequence',
        'icon',
        'isactive'
    ];
    protected $order = ['name' => 'ASC'];
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
