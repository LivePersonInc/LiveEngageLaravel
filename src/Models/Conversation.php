<?php

namespace LivePersonInc\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use LivePersonInc\LiveEngageLaravel\Collections\Transcript;
use LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory;
use LivePersonInc\LiveEngageLaravel\Collections\AgentParticipants;

class Conversation extends Model
{
	
	protected $guarded = [];
	
	public function getMessageRecordsAttribute() {
		
		$messages = [];
		foreach ($this->attributes['messageRecords'] as $line) {
			$messages[] = new Message((array) $line);
		}
		
		return collect($messages);
		
	}	
	
	public function getAgentParticipantsAttribute() {
		
		$agents = [];
		foreach ($this->attributes['agentParticipants'] as $agent) {
			$agents[] = new MessagingAgent((array) $agent);
		}
		
		return new AgentParticipants($agents);
		
	}
	
}