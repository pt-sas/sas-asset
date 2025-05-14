<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Category;
use App\Models\M_Brand;
use App\Models\M_Product;
use App\Models\M_Subcategory;
use App\Models\M_Type;
use App\Models\M_Variant;
use App\Models\M_SparePart;
use Config\Services;

class SparePart extends BaseController
{
    public function __construct()
    {
        $this->request = Services::request();
        $this->model = new M_SparePart($this->request);
        $this->entity = new \App\Entities\SparePart();
    }

    public function index()
    {
        return $this->template->render('masterdata/parts/v_parts');
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
                $ID = $value->md_sparepart_id;

                $number++;

                $row[] = $ID;
                $row[] = $number;
                $row[] = $value->value;
                $row[] = $value->name;
                $row[] = $value->category;
                $row[] = active($value->isactive);
                $row[] = $this->template->tableButton($ID);
                $data[] = $row;
            endforeach;

            $result = [
                'draw'              => $this->request->getPost('draw'),
                'recordsTotal'      => $this->datatable->countAll($table, $select, $order, $sort, $search),
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

            $post['md_variant_id'] = $post['md_variant_id'] ?: NULL;

            try {
                if (!$this->validation->run($post, 'parts')) {
                    $response = $this->field->errorValidation($this->model->table, $post);
                } else {
                    $this->entity->fill($post);
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
        $mBrand = new M_Brand($this->request);
        $mCategory = new M_Category($this->request);
        $mSubCategory = new M_Subcategory($this->request);
        $mType = new M_Type($this->request);
        $mVariant = new M_Variant($this->request);

        if ($this->request->isAJAX()) {
            try {
                $list = $this->model->where($this->model->primaryKey, $id)->findAll();

                if (!empty($list[0]->getProductCategoryId())) {
                    $rowPro = $mCategory->where($mCategory->primaryKey, $list[0]->getProductCategoryId())->first();
                    $list = $this->field->setDataSelect($mCategory->table, $list, 'product_category_id', $rowPro->getCategoryId(), $rowPro->getName());
                }

                $rowBrand = $mBrand->find($list[0]->getBrandId());
                $rowCategory = $mCategory->find($list[0]->getCategoryId());

                $list = $this->field->setDataSelect($mBrand->table, $list, $mBrand->primaryKey, $rowBrand->getBrandId(), $rowBrand->getName());
                $list = $this->field->setDataSelect($mCategory->table, $list, $mCategory->primaryKey, $rowCategory->getCategoryId(), $rowCategory->getName());

                if (!empty($list[0]->getSubCategoryId())) {
                    $rowSub = $mSubCategory->getListSub([$mSubCategory->table . '.' . $mSubCategory->primaryKey => $list[0]->getSubCategoryId()])->getRow();

                    $list = $this->field->setDataSelect($mSubCategory->table, $list, $mSubCategory->primaryKey, $rowSub->md_subcategory_id, $rowSub->name . '_' . $rowSub->category);
                }

                if (!empty($list[0]->getTypeId())) {
                    $rowType = $mType->getListType([$mType->table . '.' . $mType->primaryKey => $list[0]->getTypeId()])->getRow();

                    $list = $this->field->setDataSelect($mType->table, $list, $mType->primaryKey, $rowType->md_type_id, $rowType->name . '_' . $rowType->subcategory);
                }

                if (!empty($list[0]->getVariantId())) {
                    $rowVariant = $mVariant->find($list[0]->getVariantId());

                    $list = $this->field->setDataSelect($mVariant->table, $list, $mVariant->primaryKey, $rowVariant->getVariantId(), $rowVariant->getName());
                }

                $result = [
                    'header'   => $this->field->store($this->model->table, $list)
                ];

                $response = message('success', true, $result);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getCode());
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

                $docno = "SP" . $number;

                $response = message('success', true, $docno);
            } catch (\Exception $e) {
                $response = message('error', false, $e->getMessage());
            }

            return $this->response->setJSON($response);
        }
    }

    public function getList()
    {
        $mProduct = new M_Product($this->request);
        if ($this->request->isAjax()) {
            $post = $this->request->getVar();

            $partCPUComponent = ["Processor" => 100140, "MotherBoard" => 100141, "VGA" => 100142, "Case" => 100143, "PSU" => 100144];
            $whereCategory = !empty($post['name']) ? $partCPUComponent[$post['name']] : null;

            $product = !empty($post['reference']) ? $mProduct->where('name', $post['reference'])->first() : null;

            $response = [];

            try {
                if (!is_null($product)) {
                    if (isset($post['search'])) {
                        if (!is_null($whereCategory)) {
                            $list = $this->model->where(['isactive' => 'Y', 'md_category_id' => $whereCategory, 'product_category_id' => $product->getCategoryId()])
                                ->like('name', $post['search'])
                                ->orderBy('name', 'ASC')
                                ->findAll();
                        } else {
                            $list = $this->model->where(['isactive', 'Y', 'product_category_id' => $product->getCategoryId()])
                                ->like('name', $post['search'])
                                ->orderBy('name', 'ASC')
                                ->findAll();
                        }
                    } else if (!is_null($whereCategory)) {
                        $list = $this->model->where(['isactive' => 'Y', 'md_category_id' => $whereCategory, 'product_category_id' => $product->getCategoryId()])
                            ->orderBy('name', 'ASC')
                            ->findAll();
                    } else {
                        $list = $this->model->where(['isactive', 'Y', 'product_category_id' => $product->getCategoryId()])
                            ->orderBy('name', 'ASC')
                            ->findAll();
                    }

                    foreach ($list as $key => $row) :
                        $response[$key]['id'] = $row->getSparePartId();
                        $response[$key]['text'] = $row->getName();
                    endforeach;
                } else {
                    $response = message('error', false, 'Product not found');
                }
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

        $text =  $rowBrand->getName() . $separator . $rowCategory->getName();

        if (!empty($rowSubcategory)) {
            $text .= $separator . $rowSubcategory->getName();
        }

        if (!empty($rowType)) {
            $text .= $separator . $rowType->getName();
        }

        if (!empty($variant_id)) {
            $rowVariant = $variant->find($variant_id);
            $text .= $separator . $rowVariant->getName();
        }

        log_message('debug', $text);

        return $text;
    }
}
