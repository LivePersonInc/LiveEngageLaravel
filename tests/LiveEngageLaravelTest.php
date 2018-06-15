<?php

namespace LivePersonInc\LiveEngageLaravel\Tests;

use Orchestra\Testbench\TestCase;
use Carbon\Carbon;
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
		$history = LiveEngage::limit(10)->history(new Carbon('2018-05-27'), new Carbon('2018-05-31'));
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory', $history, "Actual Class type: " . get_class($history));
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\Engagement', $history->random(), "Actual Class type: " . get_class($history->random()));
	}
	
	/**
     * @covers LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory::next
     */
	public function testEngagementHistoryNext()
	{
		$history = LiveEngage::limit(10)->history(new Carbon('2018-05-27'), new Carbon('2018-05-31'))->next();
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory', $history, "Actual Class type: " . get_class($history));
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\Engagement', $history->random(), "Actual Class type: " . get_class($history->random()));
	}
	
	/**
     * @covers LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory::prev
     */
	public function testEngagementHistoryPrev()
	{
		$history = LiveEngage::limit(10)->history(new Carbon('2018-05-27'), new Carbon('2018-05-31'))->next()->prev();
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory', $history, "Actual Class type: " . get_class($history));
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Models\Engagement', $history->random(), "Actual Class type: " . get_class($history->random()));
	}

	/**
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::messagingHistory
     */	
	public function testGetMessagingHistory()
	{
		$history = LiveEngage::messagingHistory();
		$this->assertNotFalse(is_a($history, 'LivePersonInc\LiveEngageLaravel\Collections\ConversationHistory'), "Actual Class type: " . get_class($history));
		$this->assertNotFalse(is_a($history->random(), 'LivePersonInc\LiveEngageLaravel\Models\Conversation'), "Actual Class type: " . get_class($history->random()));
	}
	
	/**
     * @covers LivePersonInc\LiveEngageLaravel\LiveEngageLaravel::getAgentStatus
     */
	public function testGetAgentStatuses()
	{
		$agents = LiveEngage::getAgentStatus('17');
		$this->assertInstanceOf('LivePersonInc\LiveEngageLaravel\Collections\AgentParticipants', $agents, 'Return result was not the Humans collection');
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
		//$app = require __DIR__.(isset($_ENV['TEST_APP_PATH']) ? $_ENV['TEST_APP_PATH'] : '/../../../../bootstrap/app.php');
	
		$app = new \Illuminate\Foundation\Application(
		    realpath(__DIR__ . (isset($_ENV['SCRUT_TEST']) ? '/../' : '/../../../../'))
		);
		
		/*
		|--------------------------------------------------------------------------
		| Bind Important Interfaces
		|--------------------------------------------------------------------------
		|
		| Next, we need to bind some important interfaces into the container so
		| we will be able to resolve them when needed. The kernels serve the
		| incoming requests to this application from both the web and CLI.
		|
		*/
		
		$app->singleton(
		    \Illuminate\Contracts\Console\Kernel::class,
		    \Illuminate\Foundation\Console\Kernel::class
		);
		
		$app->singleton(
		    \Illuminate\Contracts\Debug\ExceptionHandler::class,
		    \Illuminate\Foundation\Exceptions\Handler::class
		);
	
		$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
	
		$app->loadEnvironmentFrom('.env');

		return $app;
	}
}
