<?php
/**
 * Message
 *
 * @package LivePersonInc\LiveEngageLaravel\Models
 */

namespace LivePersonInc\LiveEngageLaravel\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
	protected $guarded = [];

	protected $appends = [
		'plain_text',
		'text',
		'time',
	];
	
	public function getTextAttribute()
	{
		if ($this->type == 'TEXT_PLAIN') {
			return $this->messageData->msg->text;
		} elseif ($this->type == 'RICH_CONTENT') {
			return 'RICH_CONTENT'; // @codeCoverageIgnore
		} else {
			return isset($this->attributes['text']) ? $this->attributes['text'] : '';
		}
	}

	public function getPlainTextAttribute()
	{
		return strip_tags($this->text);
	}

	public function getTimeAttribute()
	{
		return new Carbon($this->attributes['time']);
	}

	public function __toString()
	{
		return $this->text;
	}
}
