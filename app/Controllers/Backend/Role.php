<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Datatable;
use App\Models\M_Role;
use App\Models\M_Menu;
use App\Models\M_Submenu;
use App\Models\M_AccessMenu;
use Config\Services;

class Role extends BaseController
{
	private $model;
	private $entity;
	protected $validation;
	protected $request;

	public function __construct()
	{
		$this->request = Services::request();
		$this->validation = Services::validation();
		$this->model = new M_Role($this->request);
		$this->entity = new \App\Entities\Role();
	}

	public function index()
	{
		$menu = new M_Menu($this->request);
		$submenu = new M_Submenu($this->request);

		$data = [
			'menu'		=> $menu->where('isactive', 'Y')
				->orderBy('name', 'ASC')
				->findAll(),
			'submenu'	=> $submenu->where('isactive', 'Y')
				->orderBy('name', 'ASC')
				->findAll()
		];

		return $this->template->render('backend/configuration/role/v_role', $data);
	}

	public function showAll()
	{
		$datatable = new M_Datatable($this->request);

		if ($this->request->getMethod(true) === 'POST') {
			$table = $this->model->table;
			$select = $this->model->findAll();
			$order = $this->model->column_order;
			$sort = $this->model->order;
			$search = $this->model->column_search;

			$data = [];

			$number = $this->request->getPost('start');
			$list = $datatable->getDatatables($table, $select, $order, $sort, $search);

			foreach ($list as $value) :
				$row = [];
				$ID = $value->sys_role_id;

				$number++;

				$row[] = $ID;
				$row[] = $number;
				$row[] = $value->name;
				$row[] = $value->description;
				$row[] = active($value->ismanual);
				$row[] = active($value->iscanexport);
				$row[] = active($value->iscanreport);
				$row[] = active($value->isallowmultipleprint);
				$row[] = active($value->isactive);
				$row[] = $this->template->tableButton($ID);
				$data[] = $row;
			endforeach;

			$result = [
				'draw'              => $this->request->getPost('draw'),
				'recordsTotal'      => $datatable->countAll($table),
				'recordsFiltered'   => $datatable->countFiltered($table, $select, $order, $sort, $search),
				'data'              => $data
			];

			return $this->response->setJSON($result);
		}
	}

	public function create()
	{
		if ($this->request->getMethod(true) === 'POST') {
			$post = $this->request->getVar();

			try {
				$this->entity->fill($post);
				$this->entity->setIsActive(setCheckbox(isset($post['isactive'])));
				$this->entity->setIsManual(setCheckbox(isset($post['ismanual'])));
				$this->entity->setIsCanExport(setCheckbox(isset($post['iscanexport'])));
				$this->entity->setIsCanReport(setCheckbox(isset($post['iscanreport'])));
				$this->entity->setIsAllowMultiplePrint(setCheckbox(isset($post['isallowmultipleprint'])));
				$this->entity->setCreatedBy($this->session->get('sys_user_id'));
				$this->entity->setUpdatedBy($this->session->get('sys_user_id'));

				if (!$this->validation->run($post, 'role')) {
					$response =	$this->field->errorValidation($this->model->table);
				} else {
					$result = $this->model->save($this->entity);

					$msg = $result ? notification('insert') : $result;

					$response = message('success', true, $msg);
				}
			} catch (\Exception $e) {
				$response = message('error', false, $e->getMessage());
			}

			return $this->response->setJSON($response);
		}
	}

	public function show($id)
	{
		$acessMenu = new M_AccessMenu($this->request);

		if ($this->request->isAJAX()) {
			try {
				$list = $this->model->where($this->model->primaryKey, $id)->findAll();
				$detail = $acessMenu->where($this->model->primaryKey, $id)->findAll();

				$result = [
					'header'	=> $this->field->store($this->model->table, $list),
					'line'    	=> $this->field->store($acessMenu->table, $detail, 'table')
				];

				$response = message('success', true, $result);
			} catch (\Exception $e) {
				$response = message('error', false, $e->getMessage());
			}

			return $this->response->setJSON($response);
		}
	}

	public function edit()
	{
		if ($this->request->getMethod(true) === 'POST') {
			$post = $this->request->getVar();

			try {
				$this->entity->fill($post);
				$this->entity->setRoleId($post['id']);
				$this->entity->setIsActive(setCheckbox(isset($post['isactive'])));
				$this->entity->setIsManual(setCheckbox(isset($post['ismanual'])));
				$this->entity->setIsCanExport(setCheckbox(isset($post['iscanexport'])));
				$this->entity->setIsCanReport(setCheckbox(isset($post['iscanreport'])));
				$this->entity->setIsAllowMultiplePrint(setCheckbox(isset($post['isallowmultipleprint'])));
				$this->entity->setUpdatedBy($this->session->get('sys_user_id'));

				if (!$this->validation->run($post, 'role')) {
					$response =	$this->field->errorValidation($this->model->table);
				} else {
					$result = $this->model->save($this->entity);

					$msg = $result ? notification('update') : $result;

					$response = message('success', true, $msg);
				}
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
}
