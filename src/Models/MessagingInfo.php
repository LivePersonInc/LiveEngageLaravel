<?php
/**
 * MessagingInfo
 *
 * @package LivePersonInc\LiveEngageLaravel\Models
 */

namespace LivePersonInc\LiveEngageLaravel\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MessagingInfo extends Model
{
	protected $guarded = [];
	protected $appends = [
		'startTime',
		'endTime'
	];

	public function getStartTimeAttribute()
	{
        if (isset($this->attributes['startTime']))
		    return new Carbon($this->attributes['startTime']);
	}
	
	public function getEndTimeAttribute()
	{
        if (isset($this->attributes['endTime'])) {
            if ($this->attributes['status'] == 'CLOSE') {
                return new Carbon($this->attributes['endTime']);
            } else {
                return null;
            }
        }
	}

	public function getMinutesAttribute()
	{
		return round(($this->attributes['duration'] / 1000) / 60, 2);
	}

	public function getSecondsAttribute()
	{
		return $this->attributes['duration'] / 1000;
	}

	public function getHoursAttribute()
	{
		return round($this->minutes / 60, 2);
	}
}
