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
		$item['info'] = isset($item['info']) ? new Info((array) $item['info']) : new Info();
		$item['visitorInfo'] = isset($item['visitorInfo']) ? new Visitor((array) $item['visitorInfo']) : new Visitor();
		$item['campaign'] = isset($item['campaign']) ? new Campaign((array) $item['campaign']) : new Campaign();
		$item['transcript'] = new Transcript(isset($item['transcript']) ? $item['transcript']->lines : []);

		parent::__construct($item);
	}
}
