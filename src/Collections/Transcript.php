<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\Models\Message;
use LivePersonInc\LiveEngageLaravel\LiveEngageLaravel;

class Transcript extends Collection {

	public function __construct(array $models = []) {
		
		return parent::__construct($models);
		
	}

}