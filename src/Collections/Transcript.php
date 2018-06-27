<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\Models\Message;
use LivePersonInc\LiveEngageLaravel\Models\Agent;

/**
 * Transcript class.
 * 
 * @extends Collection
 */
class Transcript extends Collection
{
	public function __construct(array $models = [], $agents = false)
	{
		$models = array_map(function($item) use ($agents) {
			if (property_exists($item, 'sentBy') && $item->sentBy == 'Agent' && $agents) {
				$item->agentDetails = $agents->findById($item->participantId);
			}
			return new Message((array) $item);
		}, $models);
		return parent::__construct($models);
	}
}
