<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_Submenu;
use App\Models\M_Menu;
use Config\Services;

class Submenu extends BaseController
{
	private $model;
	private $entity;

	public function __construct()
	{
		$this->request = Services::request();
		$this->model = new M_Submenu($this->request);
		$this->entity = new \App\Entities\Submenu();
	}

	public function index()
	{
		return $this->template->render('backend/configuration/submenu/v_submenu');
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
				$ID = $value->sys_submenu_id;

				$number++;

				$row[] = $ID;
				$row[] = $number;
				$row[] = $value->name;
				$row[] = $value->parent;
				$row[] = $value->url;
				$row[] = $value->sequence;
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

			try {
				$this->entity->fill($post);
				$this->entity->setIsActive(setCheckbox(isset($post['isactive'])));
				$this->entity->setCreatedBy($this->session->get('sys_user_id'));
				$this->entity->setUpdatedBy($this->session->get('sys_user_id'));

				if (!$this->validation->run($post, 'submenu')) {
					$response =	$this->field->errorValidation($this->model->table, $post);
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
		$menu = new M_Menu($this->request);

		if ($this->request->isAJAX()) {
			try {
				$list = $this->model->where($this->model->primaryKey, $id)->findAll();

				if (!empty($list[0]->getMenuId())) {
					$rowMenu = $menu->find($list[0]->getMenuId());

					$list = $this->field->setDataSelect($menu->table, $list, $menu->primaryKey, $rowMenu->getMenuId(), $rowMenu->getName());
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

	public function edit()
	{
		if ($this->request->getMethod(true) === 'POST') {
			$post = $this->request->getVar();

			try {
				$this->entity->fill($post);
				$this->entity->setSubId($post['id']);
				$this->entity->setIsActive(setCheckbox(isset($post['isactive'])));
				$this->entity->setUpdatedBy($this->session->get('sys_user_id'));

				if (!$this->validation->run($post, 'submenu')) {
					$response =	$this->field->errorValidation($this->model->table, $post);
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
