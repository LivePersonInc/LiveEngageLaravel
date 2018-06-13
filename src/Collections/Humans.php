<?php

namespace LivePersonInc\LiveEngageLaravel\Collections;

use Illuminate\Support\Collection;
use LivePersonInc\LiveEngageLaravel\LiveEngageLaravel;

class Humans extends Collection
{
	public function getMetaDataAttribute()
	{
		return $this->attributes['_metaData'];
	}
	
	public function setMetaDataAttribute($value)
	{
		$this->attributes['_metaData'] = $value;
	}
	
	public function state($state = 'ONLINE')
	{
		$result = $this->filter(function ($value, $key) use ($state) {
			return strtolower($value->currentStatus) == strtolower($state);
		});
		
		return $result;
	}
}