<?php

namespace LivePersonInc\LiveEngageLaravel\Tests;

use Orchestra\Testbench\TestCase;
use LivePersonInc\LiveEngageLaravel\ServiceProvider;
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
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::history
     */
	public function testGetHistory()
	{
		$history = LiveEngage::history();
		$this->assertNotTrue($history->isEmpty(), 'History returned no records.');
		$this->assertNotFalse(is_a($history, 'LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory'), "Actual Class type: " . get_class($history));
		$this->assertNotFalse(is_a($history->random(), 'LivePersonInc\LiveEngageLaravel\Models\Engagement'), "Actual Class type: " . get_class($history->random()));
	}

	/**
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::messagingHistory
     */	
	public function testGetMessagingHistory()
	{
		$history = LiveEngage::messagingHistory();
		$this->assertNotTrue($history->isEmpty(), 'History returned no records.');
		$this->assertNotFalse(is_a($history, 'LivePersonInc\LiveEngageLaravel\Collections\ConversationHistory'), "Actual Class type: " . get_class($history));
		$this->assertNotFalse(is_a($history->random(), 'LivePersonInc\LiveEngageLaravel\Models\Conversation'), "Actual Class type: " . get_class($history->random()));
	}
	
	/**
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::getAgentStatus
     */
	public function testGetAgentStatuses()
	{
		$agents = LiveEngage::getAgentStatus('17');
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\Humans', $agents, 'Return result was not the Humans collection');
	}
	
	public function testClassSetters()
	{
		$object = LiveEngage::domain('msgHist');
		$this->assertContains('msghist', $object->domain, 'Domain: ' . $object->domain);
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
		$app = require __DIR__.'/../../../../bootstrap/app.php';
	
		$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
	
		$app->loadEnvironmentFrom('.env');

		return $app;
	}
}
