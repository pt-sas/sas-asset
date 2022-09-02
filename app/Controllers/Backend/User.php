<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\M_User;
use App\Models\M_Role;
use Config\Services;

class User extends BaseController
{
	private $model;
	private $entity;
	protected $validation;
	protected $request;

	public function __construct()
	{
		$this->request = Services::request();
		$this->validation = Services::validation();
		$this->model = new M_User($this->request);
		$this->entity = new \App\Entities\User();
	}

	public function index()
	{
		$role = new M_Role($this->request);

		$data = [
			'role'		=> $role->where('isactive', 'Y')
				->orderBy('name', 'ASC')
				->findAll()
		];

		return $this->template->render('backend/configuration/user/v_user', $data);
	}

	public function showAll()
	{
		if ($this->request->getMethod(true) === 'POST') {
			$table = $this->model->table;
			$select = $this->model->findAll();
			$order = $this->model->column_order;
			$sort = $this->model->order;
			$search = $this->model->column_search;

			$data = [];

			$number = $this->request->getPost('start');
			$list = $this->datatable->getDatatables($table, $select, $order, $sort, $search);

			foreach ($list as $value) :
				$row = [];
				$ID = $value->sys_user_id;

				$number++;

				$row[] = $ID;
				$row[] = $number;
				$row[] = $value->username;
				$row[] = $value->name;
				$row[] = $value->description;
				$row[] = $value->email;
				$row[] = active($value->isactive);
				$row[] = $this->template->tableButton($ID);
				$data[] = $row;
			endforeach;

			$result = [
				'draw'              => $this->request->getPost('draw'),
				'recordsTotal'      => $this->datatable->countAll($table),
				'recordsFiltered'   => $this->datatable->countFiltered($table, $select, $order, $sort, $search),
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

				if (!$this->validation->run($post, 'user')) {
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
		if ($this->request->isAJAX()) {
			try {
				$list = $this->model->detail([], $this->model->table . '.' . $this->model->primaryKey, $id);

				$result = [
					'header'    => $this->field->store($this->model->table, $list->getResult(), $list)
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

			$row = $this->model->find($post['id']);

			if ($row->password !== $post['password'])
				$row->password = $post['password'];

			try {
				$this->entity->setUserName($post['username']);
				$this->entity->setName($post['name']);
				$this->entity->setEmail($post['email']);
				$this->entity->setDescription($post['description']);
				$this->entity->setUpdatedBy($this->session->get('sys_user_id'));

				// Check password has change true
				if ($row->hasChanged('password')) {
					$this->entity->setPassword($post['password']);
					$this->entity->setDatePasswordChange(date('Y-m-d H:i:s'));
				}

				$this->entity->setUserId($post['id']);
				$this->entity->setIsActive(setCheckbox(isset($post['isactive'])));

				if (!$this->validation->run($post, 'user')) {
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
				} else {
					$list = $this->model->where('isactive', 'Y')
						->orderBy('name', 'ASC')
						->findAll(5);
				}

				foreach ($list as $key => $row) :
					$response[$key]['id'] = $row->getUserId();
					$response[$key]['text'] = $row->getName();
				endforeach;
			} catch (\Exception $e) {
				$response = message('error', false, $e->getMessage());
			}

			return $this->response->setJSON($response);
		}
	}
}
