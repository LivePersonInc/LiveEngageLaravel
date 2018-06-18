<?php

namespace LivePersonInc\LiveEngageLaravel;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use LivePersonInc\LiveEngageLaravel\Models\Info;
use LivePersonInc\LiveEngageLaravel\Models\MetaData;
use LivePersonInc\LiveEngageLaravel\Models\MessagingInfo;
use LivePersonInc\LiveEngageLaravel\Models\Payload;
use LivePersonInc\LiveEngageLaravel\Models\Visitor;
use LivePersonInc\LiveEngageLaravel\Models\Agent;
use LivePersonInc\LiveEngageLaravel\Models\Campaign;
use LivePersonInc\LiveEngageLaravel\Models\Engagement;
use LivePersonInc\LiveEngageLaravel\Models\Conversation;
use LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory;
use LivePersonInc\LiveEngageLaravel\Collections\AgentParticipants;
use LivePersonInc\LiveEngageLaravel\Exceptions\LiveEngageException;
use LivePersonInc\LiveEngageLaravel\Collections\ConversationHistory;

class LiveEngageLaravel
{
	private $account = false;
	private $results = [];
	private $skills = [];
	private $next = false;
	private $prev = false;
	private $start;
	private $end;
	private $config = 'services.liveperson.default';
	private $version = '1.0';
	private $history_limit = 50;
	private $history = false;
	private $context = 'interactionHistoryRecords';
	private $interactive = true;
	private $ended = true;
	private $bearer = false;

	private $domain = false;

	private $retry_limit = 5;
	private $retry_counter = 0;

	public function __get($attribute)
	{
		return $this->$attribute;
	}

	public function __construct()
	{
		$this->account = config("{$this->config}.account");
		//$this->domain = config("{$this->config}.domain");
		$this->version = config("{$this->config}.version") ?: $this->version;
	}

	public function key($key = 'default')
	{
		$this->config = "services.liveperson.$key";
		$this->__construct();

		return $this;
	}
	
	public function nonInteractive()
	{
		$this->interactive = false;
		return $this;
	}
	
	public function active()
	{
		$this->ended = false;
		return $this;
	}

	public function limit($limit)
	{
		$this->history_limit = $limit;

		return $this;
	}

	public function skills($skills)
	{
		$this->skills = $skills;

		return $this;
	}

	public function retry($limit)
	{
		$this->retry_limit = $limit;

		return $this;
	}

	public function account($accountid)
	{
		$this->account = $accountid;

		return $this;
	}

	public function domain($service)
	{
		$response = $this->requestV1("https://api.liveperson.net/api/account/{$this->account}/service/{$service}/baseURI.json?version={$this->version}", 'GET');
		
		$this->domain = $response->baseURI;

		return $this;
	}

	public function visitor($visitorID, $sessionID, $setData = false)
	{
		$this->domain('smt');

		if ($setData) {
			$url = "https://{$this->domain}/api/account/{$this->account}/monitoring/visitors/{$visitorID}/visits/current/events?v=1&sid={$sessionID}";

			return $this->requestV1($url, 'POST', $setData);
		} else {
			$url = "https://{$this->domain}/api/account/{$this->account}/monitoring/visitors/{$visitorID}/visits/current/state?v=1&sid={$sessionID}";

			return $this->requestV1($url, 'GET');
		}
	}

	final public function retrieveHistory(Carbon $start, Carbon $end, $url = false)
	{
		$this->domain('engHistDomain');

		$url = $url ?: "https://{$this->domain}/interaction_history/api/account/{$this->account}/interactions/search?limit={$this->history_limit}&offset=0";

		$start_str = $start->toW3cString();
		$end_str = $end->toW3cString();

		$data = new Payload([
			'interactive' => $this->interactive,
			'ended' => $this->ended,
			'start' => [
				'from' => strtotime($start_str) . '000',
				'to' => strtotime($end_str) . '000',
			],
			'skillIds' => $this->skills
		]);

		return $this->requestV1($url, 'POST', $data);
	}

	final public function retrieveMsgHistory(Carbon $start, Carbon $end, $url = false)
	{
		$this->domain('msgHist');

		$url = $url ?: "https://{$this->domain}/messaging_history/api/account/{$this->account}/conversations/search?limit={$this->history_limit}&offset=0&sort=start:desc";

		$start_str = $start->toW3cString();
		$end_str = $end->toW3cString();

		$data = new Payload([
			'status' => $this->ended ? ['CLOSE'] : ['OPEN', 'CLOSE'],
			'start' => [
				'from' => strtotime($start_str) . '000',
				'to' => strtotime($end_str) . '000',
			],
			'skillIds' => $this->skills
		]);
		
		return $this->requestV1($url, 'POST', $data);
	}
	
	public function getAgentStatus($skills)
	{
		$skills = is_array($skills) ? $skills : [$skills];
	
		$this->domain('msgHist');
		
		$url = "https://{$this->domain}/messaging_history/api/account/{$this->account}/agent-view/status";
		
		$data = ['skillIds' => $skills];
		
		$response = $this->requestV1($url, 'POST', $data);
		$collection = new AgentParticipants($response->agentStatusRecords);
		$collection->metaData = new MetaData((array) $response->_metadata);
		
		return $collection;
		
	}
	
	public function messagingHistory(Carbon $start = null, Carbon $end = null)
	{
		$this->retry_counter = 0;

		$start = $start ?: (new Carbon())->today();
		$end = $end ?: (new Carbon())->today()->addHours(23)->addMinutes(59);

		$results_object = $this->retrieveMsgHistory($start, $end);
		
		$results_object->_metadata->start = $start;
		$results_object->_metadata->end = $end;
	
		$meta = new MetaData((array) $results_object->_metadata);
		
		$collection = new ConversationHistory($results_object->conversationHistoryRecords);
		$collection->metaData = $meta;
		
		return $collection;
			
	}

	public function history(Carbon $start = null, Carbon $end = null)
	{
		$this->retry_counter = 0;

		$start = $start ?: (new Carbon())->today();
		$end = $end ?: (new Carbon())->today()->addHours(23)->addMinutes(59);

		$results_object = $this->retrieveHistory($start, $end);
		
		$results_object->_metadata->start = $start;
		$results_object->_metadata->end = $end;
	
		$meta = new MetaData((array) $results_object->_metadata);
		
		$collection = new EngagementHistory($results_object->interactionHistoryRecords);
		$collection->metaData = $meta;
		
		return $collection;
			
	}
	
	public function login()
	{
		$this->domain('agentVep');
		
		$consumer_key = config("{$this->config}.key");
		$consumer_secret = config("{$this->config}.secret");
		$token = config("{$this->config}.token");
		$secret = config("{$this->config}.token_secret");
		$username = config("{$this->config}.user_name");
		
		$auth = [
			'username'		  => $username,
			'appKey'			=> $consumer_key,
			'secret'			=> $consumer_secret,
			'accessToken'		=> $token,
			'accessTokenSecret' => $secret,
		];
		
		$url = "https://{$this->domain}/api/account/{$this->account}/login?v=1.3";
		
		$response = $this->requestV1($url, 'POST', $auth);
		
		$this->bearer = $response->bearer;
		
		return $this;
	}
	
	private function requestV2($url, $method, $payload = false)
	{
		$this->login();
		
		$client = new Client();
		$args = [
			'headers' => [
				'content-type' => 'application/json',
			],
			'body' => json_encode($payload)
		];
		
		try {
			$res = $client->request($method, $url, $args);
		} catch (\Exception $e) {
			throw $e;
		} 
		
		return json_decode($res->getBody());
	}
	
	private function requestClient()
	{
		$consumer_key = config("{$this->config}.key");
		$consumer_secret = config("{$this->config}.secret");
		$token = config("{$this->config}.token");
		$secret = config("{$this->config}.token_secret");

		$stack = HandlerStack::create();
		$auth = new Oauth1([
			'consumer_key'	=> $consumer_key,
			'consumer_secret' => $consumer_secret,
			'token'		   => $token,
			'token_secret'	=> $secret,
			'signature_method'=> Oauth1::SIGNATURE_METHOD_HMAC,
		]);
		$stack->push($auth);

		$client = new Client([
			'handler' => $stack,
		]);
		
		return $client;
	}
	
	private function requestV1($url, $method, $payload = [])
	{
		$client = $this->requestClient();

		$args = [
			'auth' => 'oauth',
			'headers' => [
				'content-type' => 'application/json',
			],
			'body' => json_encode($payload)
		];

		try {
			$res = $client->request($method, $url, $args);
			$response = json_decode($res->getBody());
		} catch (\Exception $e) {
			if ($this->retry_counter < $this->retry_limit || $this->retry_limit == -1) {
				usleep(1500);
				$this->retry_counter++;
				$response = $this->requestV1($url, $payload);
			} else {
				throw $e; //new LiveEngageException("Retry limit has been exceeded ($this->retry_limit)", 100);
			}
		}

		return $response;
	}
}
