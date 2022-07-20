<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\Template;
use App\Libraries\Field;
use App\Libraries\Access;

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

		$this->session = \Config\Services::session();
		$this->language = \Config\Services::language();

		if (!empty($this->session->lang)) {
			$this->session->lang;
		} else {
			$this->session->lang = 'en';
		}

		$this->language->setLocale($this->session->lang);

		/** Drafted = DR */
		$this->DOCSTATUS_Drafted = "DR";
		/** Completed = CO */
		$this->DOCSTATUS_Completed = "CO";
		/** Approved = AP */
		$this->DOCSTATUS_Approved = "AP";
		/** Not Approved = NA */
		$this->DOCSTATUS_NotApproved = "NA";
		/** Voided = VO */
		$this->DOCSTATUS_Voided = "VO";
		/** Invalid = IN */
		$this->DOCSTATUS_Invalid = "IN";
		/** In Progress = IP */
		$this->DOCSTATUS_Inprogress = "IP";


		/** Inventory In */
		$this->Inventory_In = 'I+';
		/** Inventory Out */
		$this->Inventory_Out = 'I-';
		/** Movement In */
		$this->Movement_In = 'M+';
		/** Movement Out */
		$this->Movement_Out = 'M-';
	}
}
