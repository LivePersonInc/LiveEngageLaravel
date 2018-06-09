<?php

namespace LivePersonNY\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use LivePersonNY\LiveEngageLaravel\Collections\Transcript;
use LivePersonNY\LiveEngageLaravel\Collections\EngagementHistory;

class Engagement extends Model
{
	
	protected $guarded = [];
	protected $appends = [
		'transcript'
	];
	
	public function getTranscriptAttribute() {
		
		$messages = [];
		foreach ($this->attributes['transcript']->lines as $line) {
			$message = new Message();
			$message->fill((array) $line);
			$messages[] = $message;
		}
		
		return new Transcript($messages);
		
	}	
	
}