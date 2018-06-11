<?php

namespace LivePersonInc\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use LivePersonInc\LiveEngageLaravel\Collections\Transcript;
use LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory;
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
		if ($this->type == 'TEXT_PLAIN') {
			return $this->messageData->msg->text;
		} else if ($this->type == 'RICH_CONTENT') {
			return 'RICH_CONTENT';
		}
	}
	
}