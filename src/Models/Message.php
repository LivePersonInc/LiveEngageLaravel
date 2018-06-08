<?php

namespace LivePersonNY\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use LivePersonNY\LiveEngageLaravel\Collections\Transcript;
use LivePersonNY\LiveEngageLaravel\Collections\EngagementHistory;
use Carbon\Carbon;

class Message extends Model
{
	
	protected $guarded = [];
	
	public function getPlainTextAttribute() {
		return strip_tags($this->text);
	}
	
	public function getTimeAttribute() {
		return new Carbon($this->attributes['time']);
	}
	
}