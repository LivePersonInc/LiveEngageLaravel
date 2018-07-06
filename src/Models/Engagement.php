<?php
/**
 * Engagement
 *
 * @package LivePersonInc\LiveEngageLaravel\Models
 */

namespace LivePersonInc\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use LivePersonInc\LiveEngageLaravel\Collections\Transcript;

class Engagement extends Model
{
	protected $guarded = [];
	
	public function __construct(array $item)
	{
		$init = [
			'info'			=> [],
			'visitorInfo'	=> [],
			'campaign'		=> [],
			'transcript'	=> new Transcript([]),
		];
		
		$item = array_merge($init, $item);
		
		$item['info'] = new Info((array) $item['info']);
		$item['visitorInfo'] = new Visitor((array) $item['visitorInfo']);
		$item['campaign'] = new Campaign((array) $item['campaign']);
		$item['transcript'] = new Transcript($item['transcript']->lines);

		parent::__construct($item);
	}
}
