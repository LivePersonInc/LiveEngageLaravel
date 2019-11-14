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
		'rich_content'
	];

	public function getTextAttribute()
	{
		if ($this->type == 'TEXT_PLAIN') {
			return $this->messageData->msg->text;
		} elseif ($this->type == 'RICH_CONTENT') {
			return 'RICH CONTENT';
		} else {
			return isset($this->attributes['text']) ? $this->attributes['text'] : '';
		}
	}

	public function getRichContentAttribute()
	{
		if ($this->type == 'RICH_CONTENT') {
			return json_decode($this->messageData->richContent->content);
		} else {
			return 'RICH_CONTENT';
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

	private function parseRichContent()
	{
		$content = json_decode($this->messageData->richContent->content);

		$type = $content->type;
		$tag = $content->tag;
		$elements = $content->elements;

		$output = '';

		if ($tag == 'button') {
			foreach ($elements as $button) {
				if ($button->type != 'button') continue;
				$output .= "[{$button->title}]";
			}
			return $output;
		} else {
			return 'RICH_CONTENT';
		}
	}
}
