<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\Template;
use App\Libraries\Field;
use App\Libraries\Access;
use Config\Services;
use App\Models\M_Datatable;

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
		$this->template = new Template();
		$this->field = new Field();
		$this->access = new Access();
		$this->session = Services::session();
		$this->language = Services::language();
		$this->validation = Services::validation();
		$this->datatable = new M_Datatable($request);

		if (!empty($this->session->lang)) {
			$this->session->lang;
		} else {
			$this->session->lang = 'en';
		}

		$this->language->setLocale($this->session->lang);
	}
}
