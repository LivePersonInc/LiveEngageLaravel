<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\Models\Message;

class Transcript extends Collection
{
	public function __construct(array $models = [])
	{
		$models = array_map(function($item) {
			return new Message((array) $item);
		}, $models);
		return parent::__construct($models);
	}
}
