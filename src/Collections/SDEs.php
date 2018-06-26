<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\Models\SDE;

class SDEs extends Collection
{
	public function __construct(array $models = [])
	{
		$models = array_map(function($item) {
			return new SDE((array) $item);
		}, $models);
		
		parent::__construct($models);
	}
	
	public function getSDE($type)
	{
		
		return $this->firstWhere('sdeType', $type);
		
	}
	
}