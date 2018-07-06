<?php
/**
 * Agent
 *
 * @package LivePersonInc\LiveEngageLaravel\Models
 */

namespace LivePersonInc\LiveEngageLaravel\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use LivePersonInc\LiveEngageLaravel\Facades\LiveEngageLaravel as LiveEngage;

/**
 * Agent class.
 * 
 * @extends Model
 */
class Agent extends Model
{
	/**
	 * guarded
	 * 
	 * (default value: [])
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $guarded = [];
	
	/**
	 * userTypes
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $userTypes = [
		0 => 'System',
		1 => 'Human',
		2 => 'Bot'
	];
	
	/**
	 * getUserTypeNameAttribute function.
	 * 
	 * @access public
	 * @return void
	 * @codeCoverageIgnore
	 */
	public function getUserTypeNameAttribute()
	{
		$typeid = isset($this->attributes['userTypeId']) ? $this->attributes['userTypeId'] : $this->attributes['userType'];
		return $this->userTypes[$typeid];
	}
	
	/**
	 * getLastUpdatedTimeAttribute function.
	 * 
	 * @access public
	 * @return \Carbon\Carbon
	 * @codeCoverageIgnore
	 */
	public function getLastUpdatedTimeAttribute()
	{
		return new Carbon($this->attributes['lastUpdatedTime']);
	}
	
	/**
	 * getCurrentStatusStartTimeAttribute function.
	 * 
	 * @access public
	 * @return \Carbon\Carbon
	 * @codeCoverageIgnore
	 */
	public function getCurrentStatusStartTimeAttribute()
	{
		return new Carbon($this->attributes['currentStatusStartTime']);
	}
	
	/**
	 * getCurrentStatusReasonStartTimeAttribute function.
	 * 
	 * @access public
	 * @return \Carbon\Carbon
	 * @codeCoverageIgnore
	 */
	public function getCurrentStatusReasonStartTimeAttribute()
	{
		return new Carbon($this->attributes['currentStatusReasonStartTime']);
	}
	
	/**
	 * getStatusMinutesAttribute function.
	 * 
	 * @access public
	 * @return void
	 * @codeCoverageIgnore
	 */
	public function getStatusMinutesAttribute()
	{
		return ($this->attributes['currentStatusDuration'] / 1000) / 60;
	}
	
	/**
	 * getAvatarAttribute function.
	 * 
	 * @access public
	 * @return void
	 * @codeCoverageIgnore
	 */
	public function getAvatarAttribute()
	{
		return isset($this->attributes['pictureUrl']) ? $this->attributes['pictureUrl'] : null;
	}
	
}