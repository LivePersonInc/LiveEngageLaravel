<?php

namespace LivePersonNY\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use LivePersonNY\LiveEngageLaravel\Collections\Transcript;
use LivePersonNY\LiveEngageLaravel\Collections\EngagementHistory;
use LivePersonNY\LiveEngageLaravel\Collections\AgentParticipants;

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