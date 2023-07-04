<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Product;
use App\Models\M_Type;
use App\Models\M_Subcategory;
use App\Models\M_Category;
use App\Models\M_Brand;
use App\Models\M_Employee;
use App\Models\M_Variant;
use Config\Services;

class Product extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_Product($this->request);
        $this->entity = new \App\Entities\Product();
    }

    public function index()
    {
        return $this->template->render('masterdata/product/v_product');
    }

    public function showAll()
    {
        if ($this->request->getMethod(true) === 'POST') {
            $table = $this->model->table;
            $select = $this->model->getSelect();
            $join = $this->model->getJoin();
            $order = $this->model->column_order;
            $sort = $this->model->order;
            $search = $this->model->column_search;

            $data = [];

            $number = $this->request->getPost('start');
            $list = $this->datatable->getDatatables($table, $select, $order, $sort, $search, $join);

            foreach ($list as $value) :
                $row = [];
                $ID = $value->md_product_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->value;
                $row[] = $value->name;
                $row[] = $value->brand;
                $row[] = $value->category;
                $row[] = $value->subcategory;
                $row[] = $value->type;
                $row[] = $value->variant;
                $row[] = $value->description;
                $row[] = active($value->isactive);
                $row[] = $this->template->tableButton($ID);
                $data[] = $row;
            endforeach;

            $result = [
                'draw'              => $this->request->getPost('draw'),
                'recordsTotal'      => $this->datatable->countAll($table),
                'recordsFiltered'   => $this->datatable->countFiltered($table, $select, $order, $sort, $search, $join),
                'data'              => $data
            ];

            return $this->response->setJSON($result);
        }
    }

    public function create()
    {
        if ($this->request->getMethod(true) === 'POST') {
            $post = $this->request->getVar();

            if (!empty($post['md_brand_id']) && !empty($post['md_category_id']) && !empty($post['md_subcategory_id']) && !empty($post['md_type_id'])) {
                $post['name'] = $this->merge_name($post['md_brand_id'], $post['md_category_id'], $post['md_subcategory_id'], $post['md_type_id'], $post['md_variant_id']);
            }

            try {
                $this->entity->fill($post);

                if (!$this->validation->run($post, 'product')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    $response = $this->save();
                }
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function show($id)
    {
        $brand = new M_Brand($this->request);
        $category = new M_Category($this->request);
        $sub = new M_Subcategory($this->request);
        $type = new M_Type($this->request);
        $variant = new M_Variant($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();

                $rowBrand = $brand->find($list[0]->getBrandId());
                $rowCategory = $category->find($list[0]->getCategoryId());

                $list = $this->field->setDataSelect($brand->table, $list, $brand->primaryKey, $rowBrand->getBrandId(), $rowBrand->getName());
                $list = $this->field->setDataSelect($category->table, $list, $category->primaryKey, $rowCategory->getCategoryId(), $rowCategory->getName());

                if (!empty($list[0]->getSubCategoryId())) {
                    $rowSub = $sub->getListSub($sub->table . '.' . $sub->primaryKey, $list[0]->getSubCategoryId())->getRow();

                    $list = $this->field->setDataSelect($sub->table, $list, $sub->primaryKey, $rowSub->md_subcategory_id, $rowSub->name . '_' . $rowSub->category);
                }

                if (!empty($list[0]->getTypeId())) {
                    $rowType = $type->getListType($type->table . '.' . $type->primaryKey, $list[0]->getTypeId())->getRow();

                    $list = $this->field->setDataSelect($type->table, $list, $type->primaryKey, $rowType->md_type_id, $rowType->name . '_' . $rowType->subcategory);
                }

                if (!empty($list[0]->getVariantId())) {
                    $rowVariant = $type->getListVariant($variant->table . '.' . $variant->primaryKey, $list[0]->getVariantId())->getRow();

                    $list = $this->field->setDataSelect($variant->table, $list, $variant->primaryKey, $rowVariant->md_type_id, $rowVariant->name . '_' . $rowVariant->subcategory);
                }

                $result = [
                    'header'    => $this->field->store($this->model->table, $list)
                ];

                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function destroy($id)
    {
        if ($this->request->isAJAX()) {
            try {
                $result = $this->model->delete($id);
                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function getSeqCode()
    {
        if ($this->request->isAJAX()) {
            try {
                $number = $this->model->countAll();

                $number += 1;
                while (strlen($number) < 5) {
                    $number = "0" . $number;
                }

                $docno = "PR" . $number;

                $response = message('success', true, $docno);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function merge_name($brand_id, $category_id, $subcategory_id = null, $type_id = null, $variant_id = null)
    {
        $brand = new M_Brand($this->request);
        $category = new M_Category($this->request);
        $subcategory = new M_Subcategory($this->request);
        $type = new M_Type($this->request);
        $variant = new M_Variant($this->request);

        $separator = ' / ';

        $rowBrand = $brand->find($brand_id);
        $rowCategory = $category->find($category_id);
        $rowSubcategory = $subcategory->find($subcategory_id);
        $rowType = $type->find($type_id);
        $rowVariant = $variant->find($variant_id);

        $text =  $rowBrand->getName() . $separator . $rowCategory->getName();

        if (!empty($rowSubcategory)) {
            $text .= $separator . $rowSubcategory->getName();
        }

        if (!empty($rowType)) {
            $text .= $separator . $rowType->getName();
        }

        if (!empty($rowVariant)) {
            $text .= $separator . $rowVariant->getName();
        }

        return $text;
    }

    public function getList()
    {
        if ($this->request->isAjax()) {
            $post = $this->request->getVar();

            $response = [];

            try {
                if (isset($post['search'])) {
                    $list = $this->model->where('isactive', 'Y')
                        ->like('name', $post['search'])
                        ->orderBy('name', 'ASC')
                        ->findAll();
                } else if (isset($post['name'])) {
                    $list = $this->model->where('isactive', 'Y')
                        ->like('name', $post['name'], 'both')
                        ->orderBy('name', 'ASC')
                        ->findAll();
                } else {
                    $list = $this->model->where('isactive', 'Y')
                        ->orderBy('name', 'ASC')
                        ->findAll();
                }

                foreach ($list as $key => $row) :
                    $response[$key]['id'] = $row->getProductId();
                    $response[$key]['text'] = $row->getName();
                endforeach;
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function showProductInfo()
    {
        $employee = new M_Employee($this->request);

        if ($this->request->isAjax()) {
            $get = $this->request->getGet();

            $dataEmployee = $employee->where('isactive', 'Y')
                ->orderBy('name', 'ASC')
                ->findAll();

            $data = [];

            if (!isset($get['data'])) {
                $list = $this->model->getProductDetail($get)->getResult();

                foreach ($list as $value) :
                    $row = [];
                    $ID = $value->md_product_id;

                    $row[] = $ID;
                    $row[] = $this->field->fieldTable('input', 'checkbox', 'check_data', null, null, null, 'checked', null, $ID);
                    $row[] = $value->name;
                    $row[] = $this->field->fieldTable('input', 'text', 'qtyentered', 'number', null, null, null, null, null, 70);
                    $row[] = $this->field->fieldTable('input', 'text', 'unitprice', 'rupiah', null, $get['isfree'] === 'Y' ? 'readonly' : null, null, null, $get['isfree'] === 'Y' ? 0 : null, 125);
                    $row[] = $this->field->fieldTable('input', 'checkbox', 'isspare');
                    $row[] = $this->field->fieldTable('select', null, 'employee_id', null, null, null, null, $dataEmployee, null, 200, 'md_employee_id', 'name');
                    $data[] = $row;
                endforeach;
            }

            $result = [
                'data'              => $data
            ];

            return $this->response->setJSON($result);
        }
    }
}
