<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_Depreciation extends Model
{
    protected $table      = 'trx_depreciation';
    protected $primaryKey = 'trx_depreciation_id';
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\Depreciation';
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

    public function doInsert(array $data)
    {
        if (!is_array($data))
            return false;

        if (empty($data))
            return false;

        if (is_array($data)) {
            $result = $this->builder->insertBatch($data);

            return $result > 0 ? true : false;
        }

        return false;
    }
}
