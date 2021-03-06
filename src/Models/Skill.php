<?php
/**
 * Skill
 *
 * @package LivePersonInc\LiveEngageLaravel\Models
 */

namespace LivePersonInc\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use LivePersonInc\LiveEngageLaravel\Facades\LiveEngageLaravel as LiveEngage;

class Skill extends Model
{
	protected $guarded = [];
	
	public function __construct($skill)
	{
		if (is_int($skill)) {
			$skill = LiveEngage::getSkill($skill);
		}
		parent::__construct((array) $skill);
	}
}
