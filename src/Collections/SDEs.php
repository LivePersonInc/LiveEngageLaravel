<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\Models\SDE;

/**
 * SDEs class.
 * 
 * @extends Collection
 */
class SDEs extends Collection
{
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param array $models (default: [])
	 * @return void
	 */
	public function __construct(array $models = [])
	{
		$models = array_map(function($item) {
			return new SDE((array) $item);
		}, $models);
		
		parent::__construct($models);
	}
	
	/**
	 * getSDE function.
	 * 
	 * @access public
	 * @param mixed $type
	 * @return mixed
	 */
	public function getSDE($type)
	{
		
		return $this->firstWhere('sdeType', $type);
		
	}
	
}