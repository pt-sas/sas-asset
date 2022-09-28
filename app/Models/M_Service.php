<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;
use App\Models\M_ServiceDetail;

class M_Service extends Model
{
    protected $table      = 'trx_service';
    protected $primaryKey = 'trx_service_id';
    protected $allowedFields = [
        'documentno',
        'servicedate',
        'md_supplier_id',
        'docstatus',
        'grandtotal',
        'description',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps    = true;
    protected $returnType       = 'App\Entities\Service';
    protected $allowCallbacks   = true;
    protected $beforeInsert     = [];
    protected $afterInsert      = [];
    protected $beforeUpdate     = [];
    protected $afterUpdate      = [];
    protected $beforeDelete     = [];
    protected $afterDelete      = ['deleteDetail'];
    protected $column_order = [
        '', // Hide column
        '', // Number column
        'trx_service.documentno',
        'trx_service.servicedate',
        'md_supplier.name',
        'trx_service.grandtotal',
        'trx_service.docstatus',
        'sys_user.name',
        'trx_service.description'
    ];
    protected $column_search = [
        'trx_service.documentno',
        'trx_service.servicedate',
        'md_supplier.name',
        'trx_service.grandtotal',
        'trx_service.docstatus',
        'sys_user.name',
        'trx_service.description'
    ];
    protected $order = ['created_at' => 'DESC'];
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
            'md_supplier.name as supplier,
            sys_user.name as createdby';

        return $sql;
    }

    public function getJoin()
    {
        $sql = [
            $this->setDataJoin('md_supplier', 'md_supplier.md_supplier_id = ' . $this->table . '.md_supplier_id', 'left'),
            $this->setDataJoin('sys_user', 'sys_user.sys_user_id = ' . $this->table . '.created_by', 'left')
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

    public function getInvNumber()
    {
        $month = date('m');

        $this->builder->select('MAX(RIGHT(documentno,4)) AS documentno');
        $this->builder->where("DATE_FORMAT(servicedate, '%m')", $month);
        $sql = $this->builder->get();

        $code = "";
        if ($sql->getNumRows() > 0) {
            foreach ($sql->getResult() as $row) {
                $doc = ((int)$row->documentno + 1);
                $code = sprintf("%04s", $doc);
            }
        } else {
            $code = "0001";
        }

        $prefix = "SR" . date('ym') . $code;

        return $prefix;
    }

    public function deleteDetail(array $rows)
    {
        $serviceDetail = new M_ServiceDetail($this->request);
        $serviceDetail->where($this->primaryKey, $rows['id'])->delete();
    }
}
