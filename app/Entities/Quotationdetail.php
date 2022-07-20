<?php

namespace App\Entities;

use CodeIgniter\Entity;

class QuotationDetail extends Entity
{
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [];
}