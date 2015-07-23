<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 21/10/2014
 * Time: 20:26
 */

namespace Cpyree\SynologyBundle\Services;


use Cpyree\SynologyBundle\Webapi\WebApiResponse;
use Monolog\Logger;

/**
 * Class SynologySession
 * @package Cpyree\SynologyBundle\Services
 */
class SynologySession
{

	/**
	 * @var
	 */
	private $cl;
	/**
	 * @var
	 */
	private $host;
	/**
	 * @var
	 */
	private $port;
	/**
	 * @var
	 */
	private $scheme;
	/**
	 * @var
	 */
	private $password;
	/**
	 * @var
	 */
	private $login;
	/**
	 * @var Logger
	 */
	private $logger;
	/**
	 * @var
	 */
	private $baseUrl;
	/**
	 * @var
	 */
	private $loginUrlParams;
	/**
	 * @var
	 */
	private $zone;
	/**
	 * @var
	 */
	private $lastResponse;
	/**
	 * @var
	 */
	private $sid;
	/**
	 * @var
	 */
	private $apiList;

	/**
	 * @var bool
	 */
	private $isLogin = false;

	/**
	 * @var string
	 */
	private $method = "POST";

	/**
	 * @param Logger $logger
	 * @param $host
	 * @param $login
	 * @param $password
	 * @param string $scheme
	 * @param string $port
	 * @param string $zone
	 */
	public function __construct(Logger $logger, $host, $login, $password, $scheme = "http", $port = "5000", $zone = "FileStation")
	{
		$this
			->setLogger($logger)
			->setHost($host)
			->setLogin($login)
			->setPassword($password)
			->setScheme($scheme)
			->setPort($port)
			->setZone($zone)
			->setBaseUrl()
			->setLoginUrlParams();
	}

	/**
	 * @param Logger $logger
	 * @return $this
	 */
	private function setLogger(Logger $logger)
	{
		$this->logger = $logger;
		return $this;
	}

	/**
	 * @param mixed $password
	 * @return $this
	 */
	public function setPassword($password)
	{
		$this->password = $password;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function login()
	{
		$this->setMethod('GET');
		$r = $this->send("auth.cgi", $this->getLoginUrlParams());
		if ($r->is(('success'))) {
			$this->debug("Login succesfull");
			$this->setSid($r->get('sid'));
			$this->updateApiList();
			$this->isLogin = true;
			return true;
		} else {
			$this->debug("login error");
			//throw new \Exception('Synology login Error, check your credentials or connection parameters');
		}
		return false;
	}

	/**
	 * @param string $method
	 * @return $this
	 * @throws \Exception
	 */
	public function setMethod($method = 'POST')
	{
		if (!in_array($method, array('POST', 'GET', 'PUT'))) {
			throw new \Exception('Method is not allowed here');
		}
		$this->method = $method;
		return $this;
	}

	/**
	 * @param $url
	 * @param string $content
	 * @return WebApiResponse
	 * @throws \Exception
	 */
	public function send($cgiEntry, $params, $content = "json")
	{
		$this->iniCurl($cgiEntry, $params);
		$response = $this->execCurl();
		$this->closeCurl();
		//print_r(json_decode($response, true));
		$response = new WebApiResponse(json_decode($response, true));
		$this->lastResponse = $response;

		return $response;;
	}

	/**
	 * @param $url
	 */
	private function iniCurl($cgiEntry, $params = array())
	{

		$header = array(
			"Host: {$this->getHost()}",
			"User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:31.0) Gecko/20100101 Firefox/33.0",
			"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
			"Accept-Language: fr,fr-fr;q=0.8,en-us;q=0.5,en;q=0.3",
			"Connection: keep-alive"
		);

		$url = $this->getBaseUrl() . "/webapi/" . $cgiEntry;

		if (array_key_exists('_sid', $params) === false && $this->isLogin()) {
			$params['_sid'] = $this->getSid();
		}


		$this->cl = curl_init();

		if ($this->getMethod() === "POST") {

			curl_setopt($this->cl, CURLOPT_POST, 1);
			curl_setopt($this->cl, CURLOPT_URL, $url);
			curl_setopt($this->cl, CURLOPT_POSTFIELDS, http_build_query($params, null, '&', PHP_QUERY_RFC3986));
			$header[] = "Content-Type: application/x-www-form-urlencoded";
			print_r($url."?".http_build_query($params, null, '&', PHP_QUERY_RFC3986));
			$this->debug("make request to : " . $url, $params);

		} else {
			$url = $url . "?" . http_build_query($params, null, '&', PHP_QUERY_RFC3986);
			$this->debug("make request to : " . $url);
			curl_setopt($this->cl, CURLOPT_URL, $url);

		}
		curl_setopt($this->cl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->cl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->cl, CURLOPT_REFERER, $this->getHost());
		//curl_setopt($this->cl, CURLOPT_HEADER, true);
		curl_setopt($this->cl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($this->cl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($this->cl, CURLOPT_HTTPHEADER, $header);
	}

	/**
	 * @return mixed
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * @param mixed $host
	 * @return $this
	 */
	public function setHost($host)
	{
		$this->host = $host;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getBaseUrl()
	{
		return $this->baseUrl;
	}

	/**
	 * @return bool
	 */
	public function isLogin()
	{
		return $this->isLogin;
	}

	/**
	 * @return mixed
	 */
	private function getSid()
	{
		return $this->sid;
	}

	/**
	 * @return string
	 */
	private function getMethod()
	{
		return $this->method;
	}

	/**
	 * @param $string
	 * @param array $p
	 */
	private function debug($string, $p = array())
	{
		$this->getLogger()->debug("Synology ({$this->getSid()}): " . $string, $p);
	}

	/**
	 * @return Logger
	 */
	private function getLogger()
	{
		return $this->logger;
	}

	/**
	 * @throws \Exception
	 * @return mixed
	 */
	private function execCurl()
	{

		$r = curl_exec($this->cl);
		if ($error = curl_error($this->cl)) {
			throw new \Exception(curl_errno($this->cl) . " : " . $error);
		}
		return $r;
	}

	/**
	 *
	 */
	private function closeCurl()
	{
		curl_close($this->cl);
	}

	/**
	 * @return mixed
	 */
	public function getLoginUrlParams()
	{
		return $this->loginUrlParams;
	}

	/**
	 *
	 */
	public function setLoginUrlParams()
	{
		if (!$this->getBaseUrl()) {
			$this->setBaseUrl();
		}
		$this->loginUrlParams = array(
			"api" => "SYNO.API.Auth",
			"version" => "3",
			"method" => "login",
			"account" => $this->getLogin(),
			"passwd" => $this->getPassword(),
			"session" => $this->getZone(),
			"format" => "sid"
		);
	}

	/**
	 * @param $get
	 * @return $this
	 */
	private function setSid($get)
	{
		$this->sid = $get;
		return $this;
	}

	/**
	 *
	 */
	private function updateApiList()
	{
		$r = $this->send(
			"query.cgi",
			array(
				'api' => 'SYNO.API.Info',
				'version' => '1',
				'method' => 'query',
				'query' => 'all',
			));
		$this->apiList = $r->getData();
	}

	/**
	 * @return $this
	 */
	private function setBaseUrl()
	{
		$this->baseUrl = $this->getScheme() . "://" . $this->getHost() . ":" . $this->getPort();
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getScheme()
	{
		return $this->scheme;

	}

	/**
	 * @param mixed $scheme
	 * @throws \Exception
	 * @return $this
	 */
	public function setScheme($scheme)
	{
		if (!in_array($scheme, array('http', 'https'))) throw new \Exception(sprintf("Protocol '%s' is not allowed", $scheme));
		$this->scheme = $scheme;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPort()
	{
		return $this->port;
	}

	/**
	 * @param mixed $port
	 * @return $this
	 */
	public function setPort($port = 5000)
	{
		$this->port = intval($port);
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLogin()
	{
		return $this->login;
	}

	/**
	 * @param mixed $login
	 * @return $this
	 */
	public function setLogin($login)
	{
		$this->login = $login;
		return $this;
	}

	/**
	 * @return mixed
	 */
	private function getPassword()
	{
		return $this->password;
	}

	/**
	 * @return mixed
	 */
	public function getZone()
	{
		return $this->zone;
	}

	/**
	 * @param mixed $zone
	 * @return $this
	 */
	public function setZone($zone)
	{
		$this->zone = $zone;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isConnected()
	{
		return $this->isLogin();
	}

	/**
	 * @param $api
	 * @return bool
	 */
	public function apiExist($api)
	{
		return (isset($this->apiList[$api])) ? true : false;
	}

	/**
	 * @param $api
	 * @return null
	 */
	public function getVersion($api)
	{
		return isset($this->apiList[$api]['maxVersion']) ? $this->apiList[$api]['maxVersion'] : null;
	}

	/**
	 * @param $api
	 * @return null
	 */
	public function getPath($api)
	{
		return isset($this->apiList[$api]['path']) ? $this->apiList[$api]['path'] : null;
	}

	/**
	 *
	 */
	public function getDiskInfo()
	{
		$this->send("/webman/modules/SystemInfoApp/SystemInfo.cgi", true);
	}

}