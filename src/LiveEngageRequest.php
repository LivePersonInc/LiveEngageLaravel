<?php
/**
 * LiveEngageRequest class.
 */
	
namespace LivePersonInc\LiveEngageLaravel;

use LivePersonInc\LiveEngageLaravel\Facades\LiveEngageLaravel as LiveEngage;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use LivePersonInc\LiveEngageLaravel\Models\Payload;
use LivePersonInc\LiveEngageLaravel\Exceptions\LiveEngageException;
use LivePersonInc\LiveEngageLaravel\Exceptions\LoginFailure;

/**
 * LiveEngageRequest class.
 */
class LiveEngageRequest
{
	/**
	 * config
	 * 
	 * @var mixed
	 * @access private
	 */
	private $config;
	
	/**
	 * retry_limit
	 * 
	 * (default value: 3)
	 * 
	 * @var int
	 * @access private
	 */
	private $retry_limit = 3;
	/**
	 * retry_counter
	 * 
	 * (default value: 0)
	 * 
	 * @var int
	 * @access private
	 */
	private $retry_counter = 0;
	/**
	 * bearer
	 * 
	 * @var mixed
	 * @access private
	 */
	private $bearer;
	
	/**
	 * __construct function.
	 * 
	 * @access public
	 * @param mixed $config
	 * @return void
	 */
	public function __construct($config)
	{
		$this->config = $config; // @codeCoverageIgnore
	}
	
	/**
	 * login
	 * 
	 * @access public
	 * @return this
	 */
	public function login()
	{
		/** @scrutinizer ignore-call */
		$le = LiveEngage::domain('agentVep');
		$domain = $le->domain;
		$account = $le->account;
		
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
		
		$url = "https://{$domain}/api/account/{$account}/login?v=1.3";
		
		try {
			$response = $this->V1($url, 'POST', $auth);
		} catch (\GuzzleHttp\Exception\ServerException $e) {
			throw new LoginFailure();
		}
		
		$this->bearer = $response->bearer;
		
		return $this;
	}
	
	/**
	 * V1
	 * 
	 * @access public
	 * @param string $url
	 * @param string $method
	 * @param array $payload (default: [])
	 * @return mixed
	 */
	public function V1($url, $method, $payload = null)
	{
		$client = $this->requestClient();

		$args = [
			'auth' => 'oauth',
			'headers' => [
				'content-type' => 'application/json',
			],
			'body' => json_encode($payload ?: [])
		];

		// @codeCoverageIgnoreStart
		try {
			$res = $client->request($method, $url, $args);
			$response = json_decode($res->getBody());
		} catch (\GuzzleHttp\Exception\ServerException $e) {
			throw $e;
		} catch (\GuzzleHttp\Exception\ClientException $e) {
			$code = $e->getResponse()->getStatusCode();
			if ($code == 401) {
				throw new LoginFailure();
			} else {
				throw $e;
			}
		} catch (\Exception $e) {
			throw $e;
		}
		// @codeCoverageIgnoreEnd

		return $response;
	}
	
	/**
	 * V2
	 * 
	 * @access public
	 * @param mixed $url
	 * @param mixed $method
	 * @param mixed $payload (default: [])
	 * @param mixed $headers (default: [])
	 * @return mixed
	 */
	public function V2($url, $method, $payload = null, $headers = null)
	{
		$this->login();
		
		$client = new Client();
		$args = [
			'headers' => array_merge([
				'content-type' => 'application/json',
				'Authorization' => 'Bearer ' . $this->bearer
			], $headers ?: []),
			'body' => json_encode($payload ?: [])
		];
		
		// @codeCoverageIgnoreStart
		try {
			$res = $client->request($method, $url, $args);
		} catch (\Exception $e) {
			throw $e;
		}
		// @codeCoverageIgnoreEnd
		
		return json_decode($res->getBody());
	}
	
	/**
	 * requestClient
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
}