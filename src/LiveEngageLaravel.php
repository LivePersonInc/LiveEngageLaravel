<?php

namespace LivePersonInc\LiveEngageLaravel;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use LivePersonInc\LiveEngageLaravel\Models\Info;
use LivePersonInc\LiveEngageLaravel\Models\MetaData;
use LivePersonInc\LiveEngageLaravel\Models\AccountStatus;
use LivePersonInc\LiveEngageLaravel\Models\MessagingInfo;
use LivePersonInc\LiveEngageLaravel\Models\Payload;
use LivePersonInc\LiveEngageLaravel\Models\Visitor;
use LivePersonInc\LiveEngageLaravel\Models\Agent;
use LivePersonInc\LiveEngageLaravel\Models\Skill;
use LivePersonInc\LiveEngageLaravel\Models\Campaign;
use LivePersonInc\LiveEngageLaravel\Models\Engagement;
use LivePersonInc\LiveEngageLaravel\Models\Conversation;
use LivePersonInc\LiveEngageLaravel\Models\Message;
use LivePersonInc\LiveEngageLaravel\Collections\EngagementHistory;
use LivePersonInc\LiveEngageLaravel\Collections\Skills;
use LivePersonInc\LiveEngageLaravel\Collections\AgentParticipants;
use LivePersonInc\LiveEngageLaravel\Exceptions\LiveEngageException;
use LivePersonInc\LiveEngageLaravel\Collections\ConversationHistory;

/**
 * LiveEngageLaravel class holds all of the root package functions.
 *
 * All "setting" functions will return `$this` so method can be chained. Most methods will return a class object or Laravel collection.
 */
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
	private $revision = 0;

	private $domain = false;

	private $retry_limit = 5;
	private $retry_counter = 0;

	/**
	 * __get magic function to retrieve private properties of the class.
	 * 
	 * @access public
	 * @param mixed $attribute
	 * @return mixed
	 */
	public function __get($attribute)
	{
		return $this->$attribute;
	}

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		$this->account = config("{$this->config}.account");
		//$this->domain = config("{$this->config}.domain");
		$this->version = config("{$this->config}.version") ?: $this->version;
	}

	/**
	 * key function sets the keyset the class should use. Setting this once will be stored for script execution, but not for the session.
	 * 
	 * @access public
	 * @param string $key (default: 'default')
	 * @return this
	 */
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

	/**
	 * domain function sets the LivePerson domain for the secified service. Like `key` it is set for the execution script, but not session. It must be run each time.
	 * 
	 * @access public
	 * @param mixed $service
	 * @return this
	 */
	public function domain($service)
	{
		$response = $this->requestV1("https://api.liveperson.net/api/account/{$this->account}/service/{$service}/baseURI.json?version={$this->version}", 'GET');
		
		$this->domain = $response->baseURI;

		return $this;
	}

	/**
	 * visitor function gets or sets visitor attribute information - this only works for CHAT, not messaging.
	 * 
	 * @access public
	 * @param string $visitorID
	 * @param string $sessionID
	 * @param mixed $setData (default: false)
	 * @return mixed
	 * @codeCoverageIgnore
	 */
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
	
	/**
     * @codeCoverageIgnore
     */
	// TODO: Enable server chat api here. I actually may create new class
	public function chat()
	{
		$this->domain('conversationVep');
		
		$url = "https://{$this->domain}/api/account/{$this->account}/chat/request?v=1&NC=true";
		
		$args = [
			
		];
		$payload = new Payload($args);
		
		$response = $this->requestV1($url, 'POST', $payload);
		
		return $response;
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
	
	/**
	 * skills function gets collection of skills associated with the account.
	 * 
	 * @access public
	 * @return Collections\Skills
	 */
	public function skills()
	{
		$this->domain('accountConfigReadOnly');
		
		$url = "https://{$this->domain}/api/account/{$this->account}/configuration/le-users/skills?v=4.0";
		
		return new Skills($this->requestV2($url, 'GET'));
	}
	
	/**
	 * getSkill function gets skill object based on ID.
	 * 
	 * @access public
	 * @param int $skillId
	 * @return Models\Skill
	 */
	public function getSkill($skillId)
	{
		$this->domain('accountConfigReadOnly');
		
		$url = "https://{$this->domain}/api/account/{$this->account}/configuration/le-users/skills/{$skillId}?v=4.0";
		
		return new Skill((array) $this->requestV2($url, 'GET'));
	}
	
	/**
	 * getAgent function gets agent object based on ID.
	 * 
	 * @access public
	 * @param int $userId
	 * @return Models\Agent
	 */
	public function getAgent($userId)
	{
		$this->domain('accountConfigReadOnly');
		
		$url = "https://{$this->domain}/api/account/{$this->account}/configuration/le-users/users/{$userId}?v=4.0";
		
		return new Agent((array) $this->requestV2($url, 'GET'));
	}
	
	/**
	 * updateAgent function.
	 * 
	 * @access public
	 * @param mixed $userId
	 * @param mixed $properties
	 * @return void
	 * @codeCoverageIgnore
	 */
	public function updateAgent($userId, $properties)
	{
		$agent = $this->getAgent($userId);
		
		$this->domain('accountConfigReadWrite');
		
		$url = "https://{$this->domain}/api/account/{$this->account}/configuration/le-users/users/{$userId}?v=4.0";
		$headers = [
			'X-HTTP-Method-Override' => 'PUT',
			'if-Match' => '*'
		];
		
		return new Agent((array) $this->requestV2($url, 'PUT', $properties, $headers));
	}
	
	/**
	 * agents function gets collection of agents from account.
	 * 
	 * @access public
	 * @return Collections\AgentParticipants
	 */
	public function agents()
	{
		$this->domain('accountConfigReadOnly');
		
		$select = implode(',', [
			'id',
			'pid',
			'deleted',
			'loginName',
			'skills',
			'nickname',
			'dateCreated',
			'userTypeId',
			'isApiUser',
			'profileIds',
			'permissionGroups',
			'allowedAppKeys',
			'changePwdNextLogin',
			'maxChats',
			'skillIds',
			'lpaCreatedUser',
			'email',
			'lobs',
			'profiles',
			'fullName',
			'employeeId',
			'managedAgentGroups',
			'dateUpdated',
			'isEnabled',
			'lastPwdChangeDate'
		]);
		
		$url = "https://{$this->domain}/api/account/{$this->account}/configuration/le-users/users?v=4.0&select=$select";
		
		return new AgentParticipants($this->requestV2($url, 'GET'));
	}
	
	/**
	 * getAgentStatus function gets status of agents based on provided Skill IDs.
	 * 
	 * @access public
	 * @param int/array $skills
	 * @return Collections\AgentParticipants
	 */
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
	
	/**
	 * conversationHistory function.
	 * 
	 * @access public
	 * @param Carbon $start (default: null)
	 * @param Carbon $end (default: null)
	 * @param int/array $skills (default: [])
	 * @return Collections\ConversationHistory
	 */
	public function conversationHistory(Carbon $start = null, Carbon $end = null, $skills = [])
	{
		$this->retry_counter = 0;
		$this->skills = $skills;

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
	
	/**
	 * getConversation function.
	 * 
	 * @access public
	 * @param mixed $conversationId
	 * @return Models\Conversation
	 */
	public function getConversation($conversationId)
	{
		$this->domain('msgHist');
		
		$url = "https://{$this->domain}/messaging_history/api/account/{$this->account}/conversations/conversation/search";
		
		$data = new Payload([
			'conversationId' => $conversationId
		]);
		
		$result = $this->requestV1($url, 'POST', $data);
		if (!count($result->conversationHistoryRecords)) {
			return null;
		}
		
		return new Conversation((array) $result->conversationHistoryRecords[0]);
	}

	/**
	 * engagementHistory function.
	 * 
	 * @access public
	 * @param Carbon $start (default: null)
	 * @param Carbon $end (default: null)
	 * @param int/array $skills (default: [])
	 * @return Collections\EngagementHistory
	 */
	public function engagementHistory(Carbon $start = null, Carbon $end = null, $skills = [])
	{
		$this->retry_counter = 0;
		$this->skills = $skills;

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
	
	/**
	 * status function gets status of the account.
	 * 
	 * @access public
	 * @return Models\AccountStatus
	 */
	public function status()
	{
		$url = "https://status.liveperson.com/json?site={$this->account}";
		
		$response = $this->requestV1($url, 'GET');
		
		return new AccountStatus((array) $response);
	}
	
	/**
	 * login function.
	 * 
	 * @access public
	 * @return this
	 */
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
	
	/**
	 * requestV2 function.
	 * 
	 * @access private
	 * @param mixed $url
	 * @param mixed $method
	 * @param mixed $payload (default: [])
	 * @param mixed $headers (default: [])
	 * @return void
	 */
	private function requestV2($url, $method, $payload = [], $headers = [])
	{
		$this->login();
		
		$client = new Client();
		$args = [
			'headers' => array_merge([
				'content-type' => 'application/json',
				'Authorization' => 'Bearer ' . $this->bearer
			], $headers),
			'body' => json_encode($payload)
		];
		
		try {
			$res = $client->request($method, $url, $args);
		} catch (\Exception $e) {
			throw $e;
		} 
		
		return json_decode($res->getBody());
	}
	
	/**
	 * requestClient function.
	 * 
	 * @access private
	 * @return \GuzzleHttp\Client
	 */
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
	
	/**
	 * requestV1 - request bootstrap for older oauth supported APIs.
	 * 
	 * @access private
	 * @param string $url
	 * @param string $method
	 * @param array $payload (default: [])
	 * @return mixed
	 */
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
