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
	public $bearer;

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
	 * login function.
	 *
	 * @access public
	 * @param string $user (default: null)
	 * @param string $pass (default: null)
	 * @return object
	 */
	public function login($user = false, $pass = false)
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

		if ($user && $pass) {
			$auth = [
				'username'			=> $user,
				'password'			=> $pass
			];
		} else {
			$auth = [
				'username'			=> $username,
				'appKey'			=> $consumer_key,
				'secret'			=> $consumer_secret,
				'accessToken'		=> $token,
				'accessTokenSecret' => $secret,
			];
		}

		$url = "https://{$domain}/api/account/{$account}/login?v=1.3";

		try {
			$response = $this->get('V1', $url, 'POST', $auth, [], true);
		} catch (\GuzzleHttp\Exception\ServerException $e) {
			throw $e; //new LoginFailure();
		}

		$this->bearer = $response->body->bearer;

		$session = ['lptoken' => $this->bearer, 'lpcsrf' => $response->body->csrf];

		session($session);

		return $this;
	}

	/**
	 * V1
	 *
	 * @access public
	 * @param string $url
	 * @param string $method
	 * @param array $payload (default: [])
	 * @param bool $noauth (default: false)
	 * @return mixed
	 */
	public function V1($url, $method, $payload = null, $headers = null, $noauth = false)
	{
		$client = $this->requestClient($noauth);
		$jar = new \GuzzleHttp\Cookie\CookieJar;

		$args = [
			'auth' => 'oauth',
			'headers' => array_merge([
				'content-type' => 'application/json',
				'accept' => 'application/json'
			], $headers ?: []),
			'body' => json_encode($payload ?: [])
		];

		if ($noauth) unset($args['auth']);

		// @codeCoverageIgnoreStart
		try {
			$res = $client->request($method, $url, $args);
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

		return $res;
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
	public function V2($url, $method, $payload = null, $headers = null, $noauth = false)
	{
		if (!($token = session('lptoken', $this->bearer))) $this->login();

		$client = new Client();
		$args = [
			'headers' => array_merge([
				'content-type' => 'application/json',
				'accept' => 'application/json',
				'Authorization' => 'Bearer ' . $token
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

		return $res;
	}

	public function get($version, $url, $method, $payload = null, $headers = null, $noauth = false)
	{
		$response = $this->$version($url, $method, $payload, $headers, $noauth);

		$content = new \StdClass();
		$content->body = json_decode($response->getBody());
		$content->headers = $response->getHeaders();

		return $content;
	}

	/**
	 * requestClient
	 *
	 * @access private
	 * @return \GuzzleHttp\Client
	 * @param bool $noauth (default: false)
	 */
	private function requestClient($noauth = false)
	{
		if ($noauth) {
			return new Client();
		}

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
			'cookies' => true
		]);

		return $client;
	}

	public function refresh()
	{

	}
}
