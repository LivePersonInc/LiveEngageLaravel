<?php

namespace LivePersonInc\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use LivePersonInc\LiveEngageLaravel\Collections\AgentParticipants;
use LivePersonInc\LiveEngageLaravel\Collections\ConsumerParticipants;
use LivePersonInc\LiveEngageLaravel\Collections\Transfers;
use LivePersonInc\LiveEngageLaravel\Collections\Transcript;

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
		
		if (isset($item['transfers'])) {
			$item['transfers'] = new Transfers((array) $item['transfers']);
		}
		
		if (isset($item['agentParticipants'])) {
			$item['agentParticipants'] = new AgentParticipants($item['agentParticipants']);
		}
		
		if (isset($item['consumerParticipants'])) {
			$item['consumerParticipants'] = new AgentParticipants($item['consumerParticipants']);
		}
		
		if (isset($item['messageRecords'])) {
			$item['messageRecords'] = new Transcript($item['messageRecords']);
		}
		parent::__construct($item);
	}
}
