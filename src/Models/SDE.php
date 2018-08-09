<?php
/**
 * SDE
 *
 * @package LivePersonInc\LiveEngageLaravel\Models
 */

namespace LivePersonInc\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * SDE class.
 * 
 * @extends Model
 */
class SDE extends Model
{
	protected $guarded = [];
	
	public function getServerTimeAttribute() {
		return Carbon::createFromTimestampMs($this->attributes['serverTimeStamp']);
	}
}