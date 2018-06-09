<?php

namespace LivePersonNY\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Info extends Model
{
	
	protected $guarded = [];
	protected $appends = [
		'startTime'
	];
	
	public function getStartTimeAttribute() {
		return new Carbon($this->attributes['startTime']);
	}
	
}