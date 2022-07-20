<?php

namespace App\Models;

use CodeIgniter\Model;

use CodeIgniter\HTTP\RequestInterface;

class M_Service extends Model
{
    protected $table      = 'trx_service';
    protected $primaryKey = 'trx_service_id';
    protected $allowedFields = [
        'documentno',
        'servicedate',
        'description',
        'md_supplier_id'
    ];
    protected $useTimestamps = true;
    protected $returnType = 'App\Entities\Service';
    protected $column_order = [
        'documentno',
        'servicedate',
        'description',
        'md_supplier_id'
    ];
    protected $column_search = [
        'trx_service.documentno',
        'trx_service.servicedate',
        'md_supplier.name',
        'trx_service.description'
    ];
    protected $order = ['servicedate' => 'DESC'];
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

    private function getAll($field = null, $where = null)
    {
        $this->builder->select(
            $this->table . '.*,' .
                'md_supplier.name as supplier'
        );

        $this->builder->join('md_supplier', 'md_supplier.md_supplier_id = ' . $this->table . '.md_supplier_id', 'left');
    }

    private function getDatatablesQuery()
    {
        $post = $this->request->getVar();

        $this->getAll();

        if (isset($post['form'])) {
            $this->filterDatatable($post);
        }

        $i = 0;
        foreach ($this->column_search as $item) :
            if ($this->request->getPost('search')['value']) {
                if ($i === 0) {
                    $this->builder->groupStart();
                    $this->builder->like($item, $this->request->getPost('search')['value']);
                } else {
                    $this->builder->orLike($item, $this->request->getPost('search')['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->builder->groupEnd();
            }
            $i++;
        endforeach;

        if ($this->request->getPost('order')) {
            $this->builder->orderBy($this->column_order[$this->request->getPost('order')['0']['column']], $this->request->getPost('order')['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->builder->orderBy(key($order), $order[key($order)]);
        }
    }

    public function getDatatables()
    {
        $this->getDatatablesQuery();
        if ($this->request->getPost('length') != -1)
            $this->builder->limit($this->request->getPost('length'), $this->request->getPost('start'));
        $query = $this->builder->get();
        return $query->getResult();
    }

    public function countAll()
    {
        $sql = $this->db->table($this->table);
        return $sql->countAllResults();
    }

    public function countFiltered()
    {
        $this->getDatatablesQuery();
        return $this->builder->countAllResults();
    }

    public function filterDatatable($post)
    {
        foreach ($post['form'] as $value) :
            if (!empty($value['value'])) {
                if ($value['name'] === 'isactive')
                    $this->builder->where($this->table . '.isactive', $value['value']);
            }
        endforeach;
    }
}
