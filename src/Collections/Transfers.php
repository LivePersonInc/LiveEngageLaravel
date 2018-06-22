<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\Models\Transfer;

class Transfers extends Collection
{
	public function __construct(array $models = [])
	{
		
		$models = array_map(function($item) {
			return new Transfer((array) $item);
		}, $models);
		
		parent::__construct($models);
	}
	
	public function toSkillIds()
	{
		$array = array_map(function($item) {
			return $item['targetSkillId'];
		}, $this->toArray());
		return $array;
	}
}