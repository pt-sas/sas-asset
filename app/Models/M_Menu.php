<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;
use App\Models\M_Role;
use App\Models\M_AccessMenu;

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
    protected $useTimestamps        = true;
    protected $returnType           = 'App\Entities\Menu';
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = ['createAccessRole'];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = ['deleteAccessRole'];
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

    public function createAccessRole(array $rows)
    {
        $role = new M_Role($this->request);
        $access = new M_AccessMenu($this->request);
        $entity = new \App\Entities\AccessMenu();

        $post = $this->request->getVar();

        $list = $role->where([
            'isactive'  => 'Y',
            'ismanual'  => 'N'
        ])->findAll();

        foreach ($list as $key => $val) :
            $entity->setRoleId($val->getRoleId());
            $entity->setMenuId($rows['id']);
            $entity->setIsView('Y');
            $entity->setIsCreate('Y');
            $entity->setIsUpdate('Y');
            $entity->setIsDelete('Y');
            $entity->setCreatedBy(session()->get('sys_user_id'));
            $entity->setUpdatedBy(session()->get('sys_user_id'));

            $access->save($entity);
        endforeach;
    }

    public function deleteAccessRole(array $rows)
    {
        $access = new M_AccessMenu($this->request);
        $access->where($this->primaryKey, $rows['id'])->delete();
    }
}
