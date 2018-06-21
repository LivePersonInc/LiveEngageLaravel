<?php

namespace LivePersonInc\LiveEngageLaravel\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
	protected $guarded = [];
	
	public function __construct($array)
	{
		$array['skills'] = array_map(function($item) {
			return new Skill((array) $item);
		}, isset($array['skills']) ? $array['skills'] : []);
		parent::__construct($array);
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