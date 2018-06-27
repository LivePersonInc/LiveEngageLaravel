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
	
	public function __construct(array $item)
	{
		$item['info'] = isset($item['info']) ? new MessagingInfo((array) $item['info']) : new MessagingInfo();
		$item['visitorInfo'] = isset($item['visitorInfo']) ? new Visitor((array) $item['visitorInfo']) : new Visitor();
		$item['campaign'] = isset($item['campaign']) ? new Campaign((array) $item['campaign']) : new Campaign();
		$item['transfers'] = new Transfers(isset($item['transfers']) ? $item['transfers'] : []);
		$item['agentParticipants'] = new AgentParticipants(isset($item['agentParticipants']) ? $item['agentParticipants'] : []);
		$item['consumerParticipants'] = new ConsumerParticipants(isset($item['consumerParticipants']) ? $item['consumerParticipants'] : []);
		$item['messageRecords'] = new Transcript(isset($item['messageRecords']) ? $item['messageRecords'] : [], $item['agentParticipants']);
		$item['sdes'] = new SDEs(isset($item['sdes']->events) ? $item['sdes']->events : []);
		
		parent::__construct($item);
	}
}
