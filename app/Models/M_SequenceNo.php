<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class M_SequenceNo extends Model
{
    protected $table      = 'md_sequence_no';
    protected $primaryKey = ['md_sequence_id', 'md_groupasset_id', 'md_category_id', 'calendaryearmonth'];
    protected $allowedFields = [
        'md_sequence_id',
        'calendaryearmonth',
        'md_groupasset_id',
        'isactive',
        'currentnext',
        'maxvalue',
        'created_by',
        'updated_by',
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\SequenceNo';
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

    public function create($data, $where = [])
    {
        if (count($where) > 0)
            return $this->builder->where($where)->update($data);
        else
            return $this->builder->insert($data);
    }
}
