<?php

namespace LivePersonInc\LiveEngageLaravel\Tests;

use Orchestra\Testbench\TestCase;
use Carbon\Carbon;
use LivePersonInc\LiveEngageLaravel\ServiceProvider;
use LivePersonInc\LiveEngageLaravel\Models\Payload;
use LivePersonInc\LiveEngageLaravel\Facades\LiveEngageLaravel as LiveEngage;

class LiveEngageLaravelTest extends TestCase
{
	protected function getPackageProviders($app)
	{
		return [ServiceProvider::class];
	}

	protected function getPackageAliases($app)
	{
		return [
			'live-engage-laravel' => LiveEngageLaravel::class,
		];
	}

	/**
	 * @covers LivePersonInc\LiveEngageLaravel\Facades\LiveEngageLaravel
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::history
     * @covers LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::retrieveHistory
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::requestV1
     * @covers LivePersonInc\LiveEngageLaravel\Models\Engagement
     * @covers LivePersonInc\LiveEngageLaravel\Models\Visitor
     * @covers LivePersonInc\LiveEngageLaravel\Collections\Transcript
     * @covers LivePersonInc\LiveEngageLaravel\Models\Message
     * @covers LivePersonInc\LiveEngageLaravel\Models\Message::getTimeAttribute
     * @covers LivePersonInc\LiveEngageLaravel\Models\Info
     * @covers LivePersonInc\LiveEngageLaravel\Models\Info::getStartTimeAttribute
     * @covers LivePersonInc\LiveEngageLaravel\Models\Info::getSessionIdAttribute
     * @covers LivePersonInc\LiveEngageLaravel\Models\Info::getMinutesAttribute
     * @covers LivePersonInc\LiveEngageLaravel\Models\Info::getSecondsAttribute
     * @covers LivePersonInc\LiveEngageLaravel\Models\Info::getHoursAttribute
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::requestClient
     */
	public function testGetHistory()
	{
		$history = LiveEngage::limit(10)->history(new Carbon('2018-05-27'), new Carbon('2018-05-31'));
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory', $history, "Actual Class type: " . get_class($history));
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\Engagement', $history->random(), "Actual Class type: " . get_class($history->random()));
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\Visitor', $history->random()->visitorInfo);
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\Info', $history->random()->info);
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\Transcript', $history->random()->transcript);
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\Message', $history->random()->transcript->random());
		$this->assertInstanceOf('Carbon\Carbon', $history->random()->transcript->random()->time);
		$this->assertInstanceOf('Carbon\Carbon', $history->random()->info->startTime);
		$this->assertNotEmpty($history->random()->info->sessionId);
		$info = $history->random()->info;
		$this->assertEquals($info->minutes, round($info->seconds / 60, 2));
		$this->assertEquals($info->hours, round($info->minutes / 60, 2));
		$text = $history->random()->transcript->random();
		$this->assertNotEmpty($text, "Value: " . $text);
	}
	
	/**
	 * @covers LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory::__construct
     * @covers LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory::next
     * @covers LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory::prev
     * @use LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory::metaData
     * @covers LivePersonInc\LiveEngageLaravel\Models\Engagement
     * @covers LivePersonInc\LiveEngageLaravel\Models\MetaData
     */
	public function testEngagementHistoryNext()
	{
		$history = LiveEngage::limit(10)->history(new Carbon('2018-05-27'), new Carbon('2018-05-31'));
		$meta = $history->metaData;
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\MetaData', $meta);
		$history = $history->next();
		$this->assertEquals($meta->next->href, $history->metaData->self->href);
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory', $history, "Actual Class type: " . get_class($history));
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\Engagement', $history->random(), "Actual Class type: " . get_class($history->random()));
		
		
		$history = $history->prev();
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory', $history, "Actual Class type: " . get_class($history));
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\Engagement', $history->random(), "Actual Class type: " . get_class($history->random()));
	}
	
	/**
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::messagingHistory
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::retrieveMsgHistory
     * @covers LivePersonInc\LiveEngageLaravel\Collections\ConversationHistory
     * @covers LivePersonInc\LiveEngageLaravel\Collections\Transfers
     * @covers LivePersonInc\LiveEngageLaravel\Models\Transfer
     * @covers LivePersonInc\LiveEngageLaravel\Models\Conversation
     * @covers LivePersonInc\LiveEngageLaravel\Models\Visitor
     * @covers LivePersonInc\LiveEngageLaravel\Models\MessagingInfo
     * @covers LivePersonInc\LiveEngageLaravel\Collections\AgentParticipants
     * @covers LivePersonInc\LiveEngageLaravel\Collections\ConsumerParticipants
     * @covers LivePersonInc\LiveEngageLaravel\Collections\Transcript
     * @covers LivePersonInc\LiveEngageLaravel\Models\Message
     * @covers LivePersonInc\LiveEngageLaravel\Models\Message::__toString
     * @covers LivePersonInc\LiveEngageLaravel\Models\Message::getTimeAttribute
     * @covers LivePersonInc\LiveEngageLaravel\Models\Campaign
     * @covers LivePersonInc\LiveEngageLaravel\Models\Message::getPlainTextAttribute
     * @covers LivePersonInc\LiveEngageLaravel\Models\MessagingInfo::getStartTimeAttribute
     * @covers LivePersonInc\LiveEngageLaravel\Models\MessagingInfo::getMinutesAttribute
     * @covers LivePersonInc\LiveEngageLaravel\Models\MessagingInfo::getSecondsAttribute
     * @covers LivePersonInc\LiveEngageLaravel\Models\MessagingInfo::getHoursAttribute
     * @covers LivePersonInc\LiveEngageLaravel\Models\MessagingInfo::getStartTimeAttribute
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::requestClient
     */	
	public function testGetMessagingHistory()
	{
		$history = LiveEngage::messagingHistory();
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\ConversationHistory', $history);
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\Conversation', $history->random());
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\Campaign', $history->random()->campaign);
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\AgentParticipants', $history->random()->agentParticipants);
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\ConsumerParticipants', $history->random()->consumerParticipants);
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\Transfers', $history->random()->transfers);
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\Visitor', $history->random()->visitorInfo);
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\MessagingInfo', $history->random()->info);
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\Transcript', $history->random()->messageRecords);
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\Message', $history->random()->messageRecords->random());
		$this->assertInstanceOf('Carbon\Carbon', $history->random()->messageRecords->random()->time);
		$this->assertInstanceOf('Carbon\Carbon', $history->random()->info->startTime);
		$text = $history->random()->messageRecords->random();
		$this->assertNotEmpty($text, "Value: " . $text);
		$info = $history->random()->info;
		$this->assertEquals($info->minutes, round($info->seconds / 60, 2));
		$this->assertEquals($info->hours, round($info->minutes / 60, 2));
	}
	
	/**
     * @covers LivePersonInc\LiveEngageLaravel\Collections\ConversationHistory::next
     * @covers LivePersonInc\LiveEngageLaravel\Collections\ConversationHistory::prev
     * @covers LivePersonInc\LiveEngageLaravel\Models\Conversation
     */
	public function testConversationHistoryNext()
	{
		$history = LiveEngage::limit(10)->messagingHistory()->next();
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\ConversationHistory', $history, "Actual Class type: " . get_class($history));
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\Conversation', $history->random(), "Actual Class type: " . get_class($history->random()));
		
		$history = $history->prev();
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\ConversationHistory', $history, "Actual Class type: " . get_class($history));
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\Conversation', $history->random(), "Actual Class type: " . get_class($history->random()));
	}
	
	/**
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::getAgentStatus
     * @covers LivePersonInc\LiveEngageLaravel\Collections\AgentParticipants
     * @covers LivePersonInc\LiveEngageLaravel\Collections\AgentParticipants::findById
     * @covers LivePersonInc\LiveEngageLaravel\Collections\AgentParticipants::state
     * @covers LivePersonInc\LiveEngageLaravel\Models\Agent
     */
	public function testGetAgentStatuses()
	{
		$agents = LiveEngage::getAgentStatus('17');
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\AgentParticipants', $agents);
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\AgentParticipants', $agents->state('online'));
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\Agent', $agents->findById($agents->random()->agentId));
	}
	
	/**
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::domain
     */
	public function testClassSetters()
	{
		$object = LiveEngage::domain('msgHist');
		$this->assertContains('msghist', $object->domain, 'Domain: ' . $object->domain);
	}
	
	/**
     * @covers LivePersonInc\LiveEngageLaravel\Models\Payload
     * @covers LivePersonInc\LiveEngageLaravel\Models\Payload::__construct
     */
	public function testPayload()
	{
		$data = new Payload([
			'interactive' => false,
			'ended' => true,
			'start' => [
				'from' => time() . '000',
				'to' => time() . '000',
			],
			'skillIds' => []
		]);
		
		$this->assertObjectNotHasAttribute('skillIds', $data);
	}
	
	/**
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::active
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::__construct
     */
	public function testActive()
	{
		$object = LiveEngage::active();
		$this->assertFalse($object->ended);
	}
	
	/**
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::limit
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::__construct
     */
	public function testLimit()
	{
		$object = LiveEngage::limit(76);
		$this->assertEquals($object->history_limit, 76);
	}
	
	/**
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::account
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::__construct
     */
	public function testAccount()
	{
		$object = LiveEngage::account('75555851');
		$this->assertEquals($object->account, '75555851');
	}
	
	/**
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::nonInteractive
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::__construct
     */
	public function testNonInteractive()
	{
		$object = LiveEngage::nonInteractive();
		$this->assertFalse($object->interactive);
	}
	
	/**
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::key
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::__construct
     */
	public function testKey()
	{
		$object = LiveEngage::key();
		$this->assertEquals($object->config, "services.liveperson.default");
	}
	
	/**
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::skills
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::__construct
     */
	public function testSkills()
	{
		$object = LiveEngage::skills(['17','11']);
		$this->assertEquals($object->skills, ['17','11']);
	}
	
	/**
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::retry
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::__construct
     */
	public function testRetry()
	{
		$object = LiveEngage::retry(4);
		$this->assertEquals($object->retry_limit, 4);
	}
	
	public function setUp()
	{
		parent::setUp();
		
		config([
			'services.liveperson.default' => [
				'key' => getenv('LP_KEY'),
				'secret' => getenv('LP_SECRET'),
				'token' => getenv('LP_TOKEN'),
				'token_secret' => getenv('LP_TOKEN_SECRET'),
				'account' => getenv('LP_ACCOUNT'),
			]
		]);
	}
	
	/**
	 * Creates the application.
	 *
	 * @return \Illuminate\Foundation\Application
	 */
	
	public function createApplication()
	{
		if (getenv('SCRUT_TEST')) return parent::createApplication();
		
		$app = require __DIR__.'/../../../../bootstrap/app.php';
	
		$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
	
		$app->loadEnvironmentFrom('.env');

		return $app;
	}
}
