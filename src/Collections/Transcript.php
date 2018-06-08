<?php

namespace LivePersonNY\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonNY\LiveEngageLaravel\Models\Message;
use LivePersonNY\LiveEngageLaravel\LiveEngageLaravel;

class Transcript extends Collection {

	public function __construct(array $models = []) {
		
		return parent::__construct($models);
		
	}

}