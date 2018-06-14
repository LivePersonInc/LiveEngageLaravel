<?php

namespace LivePersonInc\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use LivePersonInc\LiveEngageLaravel\Collections\Transcript;

class Engagement extends Model
{
	protected $guarded = [];
	protected $appends = [
		'transcript',
	];
	
	public function __construct(array $item)
	{
		if (isset($item['info'])) {
			$item['info'] = new Info((array) $item['info']);
		}

		if (isset($item['visitorInfo'])) {
			$item['visitorInfo'] = new Visitor((array) $item['visitorInfo']);
		}

		if (isset($item['campaign'])) {
			$item['campaign'] = new Campaign((array) $item['campaign']);
		}
		
		if (isset($item['transcript'])) {
			$item['transcript'] = new Transcript((array) $item['transcript']);
		}
		parent::__construct($item);
	}
}
