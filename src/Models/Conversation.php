<?php
/**
 * Conversation
 *
 * @package LivePersonInc\LiveEngageLaravel\Models
 */

namespace LivePersonInc\LiveEngageLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use LivePersonInc\LiveEngageLaravel\Collections\AgentParticipants;
use LivePersonInc\LiveEngageLaravel\Collections\ConsumerParticipants;
use LivePersonInc\LiveEngageLaravel\Collections\Transfers;
use LivePersonInc\LiveEngageLaravel\Collections\Transcript;
use LivePersonInc\LiveEngageLaravel\Collections\SDEs;

/**
 * Conversation class.
 *
 * @extends Model
 */
class Conversation extends Model
{
	protected $guarded = [];

	protected $appends = [
		'textTranscript',
	];

	public function __construct(array $item)
	{
		$init = [
			'info'					=> [],
			'visitorInfo'			=> [],
			'campaign'				=> [],
			'transfers'				=> [],
			'agentParticipants'		=> [],
			'consumerParticipants'	=> [],
			'messageRecords'		=> [],
			'sdes'					=> new SDE()
		];

		$item = array_merge($init, $item);

		$item['info'] = new MessagingInfo((array) $item['info']);
		$item['visitorInfo'] = new Visitor((array) $item['visitorInfo']);
		$item['campaign'] = new Campaign((array) $item['campaign']);
		$item['transfers'] = new Transfers($item['transfers']);
		$item['agentParticipants'] = new AgentParticipants($item['agentParticipants']);
		$item['consumerParticipants'] = new ConsumerParticipants($item['consumerParticipants']);
		$item['messageRecords'] = new Transcript($item['messageRecords'], $item['agentParticipants']);
		$item['sdes'] = new SDEs($item['sdes']->events ?: []);

		parent::__construct($item);
	}

	/*public function getInfoAttribute()
	{
		return new MessagingInfo((array) $this->attributes['info']);
	}*/

	/**
	 * @codeCoverageIgnore
	 */
	public function getTextTranscriptAttribute()
	{
		return $this->messageRecords->textTranscript();
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getExportAttribute()
	{
		$info = $this->info->attributes;
		$info['transcript'] = $this->textTranscript;
		return ((object)$info);
	}

	public function extractEmail(&$matches = [])
	{
		$pattern = '/[.A-Za-z0-9_-]+@[A-Za-z0-9_-]+\.([A-Za-z0-9_-][A-Za-z0-9_]+)/'; //regex for pattern of e-mail address
        preg_match($pattern, $this->textTranscript, $matches);
        return count($matches) ? $matches[0] : null;
	}
}
