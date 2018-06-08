<?php

namespace LivePersonNY\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use LivePersonNY\LiveEngageLaravel\Collections\EngagementHistory;

class Engagement extends Model
{
	
	protected $guarded = [];
	
	public function newCollection(array $models = []) {
		
		return new EngagementHistory($models);
		
	}
	
}