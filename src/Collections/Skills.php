<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\Models\Skill;

class Skills extends Collection
{
	public function __construct(array $models = [])
	{
		
		$models = array_map(function($item) {
			return new Skill((array) $item);
		}, $models);
		
		parent::__construct($models);
	}
	
	public function toSkillIds()
	{
		$array = array_map(function($item) {
			return $item['id'];
		}, $this->toArray());
		return $array;
	}
}