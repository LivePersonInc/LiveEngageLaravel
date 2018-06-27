<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\Models\Agent;
use LivePersonInc\LiveEngageLaravel\Models\MetaData;

/**
 * AgentParticipants class.
 * 
 * @extends Collection
 */
class AgentParticipants extends Collection
{
	public $metaData;
	
	public function __construct(array $models = [])
	{
		
		$models = array_map(function($item) {
			return is_a($item, 'LivePersonInc\LiveEngageLaravel\Models\Agent') ? $item : new Agent((array) $item);
		}, $models);
		$this->metaData = new MetaData();
		parent::__construct($models);
	}
	
	public function state($state = 'ONLINE')
	{
		$result = $this->filter(function($value) use ($state) {
			return strtolower($value->currentStatus) == strtolower($state);
		});
		
		return $result;
	}
	
	public function findById($agentId)
	{
		$result = $this->filter(function($value) use ($agentId) {
			return $value->agentId == $agentId;
		});
		
		return $result->first();
	}
}
