<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\Models\Agent;

class AgentParticipants extends Collection
{
	public $metaData;
	
	public function __construct(array $models = [])
	{
		
		$models = array_map(function($item) {
			return new Agent((array) $item);
		}, $models);
		
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
			return strtolower($value->agentId) == $agentId;
		});
		
		return $result->first();
	}
}
