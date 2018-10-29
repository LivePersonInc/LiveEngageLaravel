<?php
	
namespace LivePersonInc\LiveEngageLaravel\Traits;

use LivePersonInc\LiveEngageLaravel\Facades\LiveEngageLaravel as LiveEngage;

trait Timeable
{
	
	public function averageDurationMinutes()
	{
		return $this->totalDurationMinutes() / $this->count();
	}
	
	public function averageDurationSeconds()
	{
		return $this->totalDurationSeconds() / $this->count();
	}
	
	public function totalDurationMinutes($filter = [])
	{
		return $this->sum(function($item) use ($filter) {
			return $item->info->minutes;
		});
	}
	
	public function totalDurationSeconds($filter = [])
	{
		return $this->sum(function($item) use ($filter) {
			return $item->info->seconds;
		});
	}
	
}