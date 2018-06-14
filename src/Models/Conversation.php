<?php

namespace LivePersonInc\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use LivePersonInc\LiveEngageLaravel\Collections\AgentParticipants;

class Conversation extends Model
{
	protected $guarded = [];
	
	public function __construct(array $item)
	{
		if (isset($item['info'])) {
			$item['info'] = new MessagingInfo((array) $item['info']);
		}

		if (isset($item['visitorInfo'])) {
			$item['visitorInfo'] = new Visitor((array) $item['visitorInfo']);
		}

		if (isset($item['campaign'])) {
			$item['campaign'] = new Campaign((array) $item['campaign']);
		}
		parent::__construct($item);
	}

	public function getMessageRecordsAttribute()
	{
		$messages = [];
		foreach ($this->attributes['messageRecords'] as $line) {
			$messages[] = new Message((array) $line);
		}

		return collect($messages);
	}

	public function getAgentParticipantsAttribute()
	{
		$agents = [];
		foreach ($this->attributes['agentParticipants'] as $agent) {
			$agents[] = new MessagingAgent((array) $agent);
		}

		return new AgentParticipants($agents);
	}
}
