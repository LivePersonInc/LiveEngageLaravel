<?php

namespace LivePersonNY\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use LivePersonNY\LiveEngageLaravel\Collections\Transcript;
use LivePersonNY\LiveEngageLaravel\Collections\EngagementHistory;
use Carbon\Carbon;

class Message extends Model
{
	
	protected $guarded = [];
	
	protected $appends = [
		'plaint_text',
		'time'
	];
	
	public function getTextAttribute() {
		if ($this->type == 'PLAIN') {
			return $this->messageData->msg->text;
		} else if ($this->type == 'RICH_CONTENT') {
			return 'RICH_CONTENT';
		} else {
			return isset($this->attributes['text']) ? $this->attributes['text'] : '';
		}
	}
	
	public function getPlainTextAttribute() {
		return strip_tags($this->text);
	}
	
	public function getTimeAttribute() {
		return new Carbon($this->attributes['time']);
	}
	
	public function __toString() {
		return $this->plain_text;
	}
	
}