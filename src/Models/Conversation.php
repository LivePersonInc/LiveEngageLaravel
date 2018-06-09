<?php

namespace LivePersonNY\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use LivePersonNY\LiveEngageLaravel\Collections\Transcript;
use LivePersonNY\LiveEngageLaravel\Collections\EngagementHistory;

class Conversation extends Model
{
	
	protected $guarded = [];
	protected $appends = [
		'transcript'
	];
	
	public function getMessageRecordsAttribute() {
		
		$messages = [];
		foreach ($this->attributes['messageRecords'] as $line) {
			$messages[] = new Message((array) $line);
		}
		
		return collect($messages);
		
	}	
	
}