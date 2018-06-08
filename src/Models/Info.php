<?php

namespace LivePersonNY\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use LivePersonNY\LiveEngageLaravel\Collections\Transcript;
use LivePersonNY\LiveEngageLaravel\Collections\EngagementHistory;
use Carbon\Carbon;

class Info extends Model
{
	
	protected $guarded = [];
	
	public function getStartTimeAttribute() {
		return new Carbon($this->attributes['startTime']);
	}
	
}