<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\Models\Visitor;

class ConsumerParticipants extends Collection
{
	public function __construct(array $models = [])
	{
		
		$models = array_map(function($item) {
			return new Visitor((array) $item);
		}, $models);
		
		parent::__construct($models);
	}
}
