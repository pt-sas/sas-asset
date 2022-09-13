<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\Template;
use App\Libraries\Field;
use App\Libraries\Access;
use App\Models\M_Datatable;
use App\Models\M_ChangeLog;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Config\Services;
use stdClass;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */

class BaseController extends Controller
{
	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = ['action_helper', 'url', 'date_helper'];

	protected $session;
	protected $language;
	protected $validation;
	protected $model;
	protected $entity;

	//TODO: LIBRARY
	protected $template;
	protected $field;
	protected $access;

	//TODO: EVENT
	/** Insert = I */
	protected $EVENTCHANGELOG_Insert = "I";
	/** Update = U */
	protected $EVENTCHANGELOG_Update = "U";
	/** Delete = D */
	protected $EVENTCHANGELOG_Delete = "D";
	/** Drafted = DR */
	protected $DOCSTATUS_Drafted = "DR";
	/** Completed = CO */
	protected $DOCSTATUS_Completed = "CO";
	/** Approved = AP */
	protected $DOCSTATUS_Approved = "AP";
	/** Not Approved = NA */
	protected $DOCSTATUS_NotApproved = "NA";
	/** Voided = VO */
	protected $DOCSTATUS_Voided = "VO";
	/** Invalid = IN */
	protected $DOCSTATUS_Invalid = "IN";
	/** In Progress = IP */
	protected $DOCSTATUS_Inprogress = "IP";
	/** Inventory In */
	protected $Inventory_In = 'I+';
	/** Inventory Out */
	protected $Inventory_Out = 'I-';
	/** Movement In */
	protected $Movement_In = 'M+';
	/** Movement Out */
	protected $Movement_Out = 'M-';
	/**
	 * The column used for insert int
	 *
	 * @var int
	 */
	protected $createdByField = 'created_by';
	/**
	 * The column used for update int
	 *
	 * @var int
	 */
	protected $updatedByField = 'updated_by';
	/**
	 * The message used for return value string
	 *
	 * @var string
	 */
	protected $message = '';

	/**
	 * Constructor.
	 *
	 * @param RequestInterface  $request
	 * @param ResponseInterface $response
	 * @param LoggerInterface   $logger
	 */
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.: $this->session = \Config\Services::session();

		//* Load Service
		$this->session = Services::session();
		$this->language = Services::language();
		$this->validation = Services::validation();

		//* Load Libraries 
		$this->template = new Template();
		$this->field = new Field();
		$this->access = new Access();

		//* Load Models
		$this->datatable = new M_Datatable($request);

		//? Check language 
		if (!empty($this->session->lang))
			$this->session->lang;
		else
			$this->session->lang = 'en';

		//TODO: Setup language 
		$this->language->setLocale($this->session->lang);
	}

	/**
	 * Inserts data into the database
	 * 
	 */
	public function save()
	{
		$changeLog = new M_ChangeLog($this->request);

		//* Object class Old Value 
		$oldV = new stdClass();
		//* Object class New Value 
		$newV = new stdClass();

		$columnPK = $this->model->primaryKey;
		$modelTable = $this->model->table;

		$newRecord = $this->isNew();

		$data = $this->entity;

		$data = $this->transformDataToArray($data, 'insert');

		// Must be called first so we don't
		// strip out created_at values.
		$data = $this->doStrip($data);

		try {
			foreach ($data as $key => $val) :
				if ($newRecord) {
					$newV->{$key} = $val;

					if ($this->createdByField && !array_key_exists($this->createdByField, $data))
						$newV->{$this->createdByField} = $this->access->getSessionUser();

					if ($this->updatedByField && !array_key_exists($this->updatedByField, $data))
						$newV->{$this->updatedByField} = $this->access->getSessionUser();
				} else {
					$row = $this->model->find($this->getID());

					if ($data[$key] !== $row->$key) {
						//* Old Value 
						$oldV->{$key} = $row->{$key};

						//* New Value 
						$newV->{$key} = $val;

						//TODO: Insert Change Log
						$changeLog->insertLog($modelTable, $key, $this->getID(), $oldV->{$key}, $newV->{$key}, $this->EVENTCHANGELOG_Update);

						if ($this->updatedByField && !array_key_exists($this->updatedByField, $data))
							$newV->{$this->updatedByField} = $this->access->getSessionUser();

						$newV->{$columnPK} = $this->getID();
					}
				}
			endforeach;

			$ok = $this->model->save($newV);

			if ($ok) {
				if ($newRecord) {
					//TODO: Insert Change Log
					$changeLog->insertLog($modelTable, $columnPK, $this->getID(), null, $this->getID(), $this->EVENTCHANGELOG_Insert);

					$this->message = notification("insert");
				} else {

					if (isset($newV->docstatus) && $newV->docstatus === $this->DOCSTATUS_Completed)
						$this->message = true;
					else if (isset($newV->docstatus) && $newV->docstatus === $this->DOCSTATUS_Invalid)
						$this->message = 'Document cannot be processed';
					else
						$this->message = notification("updated");
				}

				$ok = message('success', true, $this->message);
			} else {
				$ok = message('error', true, $this->message);
			}
		} catch (\Exception $e) {
			throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
		}

		return $ok;
	}

	/**
	 * Ensures that only the fields that are allowed to be updated
	 * are in the data array.
	 *
	 * @param array $data Data
	 */
	protected function doStrip(array $data): array
	{
		foreach (array_keys($data) as $key) {
			if (!in_array($key, $this->model->allowedFields, true)) {
				unset($data[$key]);
			}
		}

		return $data;
	}

	/**
	 * Transform data to array
	 *
	 * @param array|object|null $data Data
	 * @param string            $type Type of data (insert|update)
	 */
	protected function transformDataToArray($data, string $type): array
	{
		// If $data is using a custom class with public or protected
		// properties representing the collection elements, we need to grab
		// them as an array.
		if (is_object($data) && !$data instanceof stdClass) {
			$data = $this->objectToArray($data, ($type === 'update'), true);
		}

		// If it's still a stdClass, go ahead and convert to
		// an array so doProtectFields and other model methods
		// don't have to do special checks.
		if (is_object($data)) {
			$data = (array) $data;
		}

		return $data;
	}

	/**
	 * Takes a class an returns an array of it's public and protected
	 * properties as an array suitable for use in creates and updates.
	 * This method use objectToRawArray internally and does conversion
	 * to string on all Time instances
	 *
	 * @param object|string $data        Data
	 * @param bool          $onlyChanged Only Changed Property
	 * @param bool          $recursive   If true, inner entities will be casted as array as well
	 *
	 * @throws ReflectionException
	 *
	 * @return array Array
	 */
	protected function objectToArray($data, bool $onlyChanged = true, bool $recursive = false): array
	{
		$properties = $this->objectToRawArray($data, $onlyChanged, $recursive);

		return $properties;
	}

	/**
	 * Takes a class an returns an array of it's public and protected
	 * properties as an array with raw values.
	 *
	 * @param object|string $data        Data
	 * @param bool          $onlyChanged Only Changed Property
	 * @param bool          $recursive   If true, inner entities will be casted as array as well
	 *
	 * @throws ReflectionException
	 *
	 * @return array|null Array
	 */
	protected function objectToRawArray($data, bool $onlyChanged = true, bool $recursive = false): ?array
	{
		if (method_exists($data, 'toRawArray')) {
			$properties = $data->toRawArray($onlyChanged, $recursive);
		} else {
			$mirror = new ReflectionClass($data);
			$props  = $mirror->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);

			$properties = [];

			// Loop over each property,
			// saving the name/value in a new array we can return.
			foreach ($props as $prop) {
				// Must make protected values accessible.
				$prop->setAccessible(true);
				$properties[$prop->getName()] = $prop->getValue($data);
			}
		}

		return $properties;
	}

	/**
	 * 	Is new record
	 *	@return true if new
	 */
	protected function isNew()
	{
		//* Get Request POST From View 
		$post = $this->request->getVar();

		if (!$post || isset($post['id']))
			return false;

		return true;
	}

	/**
	 *  Return Single Key Record ID
	 *  @return ID or 0
	 */
	protected function getID()
	{
		//* Get Request POST From View 
		$post = $this->request->getVar();

		if (!$post || isset($post['id']))
			return $post['id'];

		if (!empty($this->model->getInsertID()))
			return $this->model->getInsertID();

		return 0;
	}
}
