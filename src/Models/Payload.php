<?php

namespace LivePersonInc\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;

class Payload extends Model
{
	protected $guarded = [];
	
	public function __construct(array $array)
	{
		$array = array_filter($array);
		parent::__construct($array);
	}
}
