<?php

namespace LivePersonInc\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;

class AccountStatus extends Model
{
	protected $guarded = [];
	
	public function __construct(array $array)
	{
		$output = [];
		foreach ($array as $key=>$value) {
			$output[snake_case($key)] = $value;
		}
		parent::__construct($output);
	}
}
