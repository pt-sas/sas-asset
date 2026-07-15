<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_GroupAssetAccount extends Model
{
    protected $table      = 'md_groupasset_acct';
    protected $primaryKey = 'md_groupasset_acct_id';
    protected $allowedFields = [
        'md_groupasset_id',
        'md_account_id',
        'name',
        'description',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\GroupAssetAccount';
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

    public function getGroupAssetAccount($where = null)
    {
        $this->builder->select("{$this->table}.*,
                                CONCAT(md_account.value,'_',md_account.name) as coa");

        $this->builder->join('md_account', "{$this->table}.md_account_id = md_account.md_account_id", 'left');

        if ($where)
            $this->builder->where($where);

        return $this->builder->get()->getResult();
    }
}
