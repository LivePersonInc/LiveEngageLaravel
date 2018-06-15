<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\Models\Visitor;
use LivePersonInc\LiveEngageLaravel\Models\MetaData;

class ConsumerParticipants extends Collection
{
	public $metaData;
	
	public function __construct(array $models = [])
	{
		
		$models = array_map(function($item) {
			return is_a($item, 'LivePersonInc\LiveEngageLaravel\Models\Visitor') ? $item : new Visitor((array) $item);
		}, $models);
		$this->metaData = new MetaData();
		parent::__construct($models);
	}
}
