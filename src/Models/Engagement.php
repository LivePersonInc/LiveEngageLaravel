<?php

namespace LivePersonInc\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use LivePersonInc\LiveEngageLaravel\Collections\Transcript;

class Engagement extends Model
{
	protected $guarded = [];
	protected $appends = [
		'transcript',
	];

	public function getTranscriptAttribute()
	{
		$messages = [];
		foreach ($this->attributes['transcript']->lines as $line) {
			$messages[] = new Message((array) $line);
		}

		return new Transcript($messages);
	}
}
