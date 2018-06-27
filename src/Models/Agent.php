<?php

namespace LivePersonInc\LiveEngageLaravel\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Agent class.
 * 
 * @extends Model
 */
class Agent extends Model
{
	protected $guarded = [];
	
	protected $userTypes = [
		1 => 'Human',
		2 => 'Bot'
	];
	
	public function getUserTypeNameAttribute()
	{
		return $this->userTypes[$this->attributes['userTypeId']];
	}
	
	public function getLastUpdatedTimeAttribute()
	{
		return new Carbon($this->attributes['lastUpdatedTime']);
	}
	
	public function getCurrentStatusStartTimeAttribute()
	{
		return new Carbon($this->attributes['currentStatusStartTime']);
	}
	
	public function getCurrentStatusReasonStartTimeAttribute()
	{
		return new Carbon($this->attributes['currentStatusReasonStartTime']);
	}
	
	public function getStatusMinutesAttribute()
	{
		return ($this->attributes['currentStatusDuration'] / 1000) / 60;
	}
}