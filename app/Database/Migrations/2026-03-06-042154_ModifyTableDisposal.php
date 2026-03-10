<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyTableDisposal extends Migration
{
    public function up()
    {
        $fields = [
            'md_supplier_id'    => ['type' => 'INT', 'constraint' => 6, 'null' => true],
            'sys_wfscenario_id' => ['type' => 'INT', 'constraint' => 6, 'null' => true]
        ];

        $this->forge->modifyColumn('trx_disposal', $fields);

        $this->db->query('ALTER TABLE trx_disposal DROP FOREIGN KEY FK_SupplierIdDisposal');
    }

    public function down()
    {
        $fields = [
            'md_supplier_id'    => ['type' => 'INT', 'constraint' => 6, 'null' => false],
            'sys_wfscenario_id' => ['type' => 'INT', 'constraint' => 6, 'null' => false]
        ];

        $this->forge->modifyColumn('trx_disposal', $fields);

        $this->db->query('ALTER TABLE trx_disposal ADD CONSTRAINT FK_SupplierIdDisposal FOREIGN KEY (md_supplier_id) REFERENCES md_supplier(md_supplier_id);');
    }
}
