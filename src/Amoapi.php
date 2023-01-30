<?php
/**
 * amoCRM API client by api-hash
 * @author Vlad Ionov <vlad@f5.com.ru>
 * @version 0.8
 */
namespace Ufee\Amo;

class Amoapi extends ApiClient
{
	const VERSION = 8;
	const SESS_LIFETIME = 900;
	const AUTH_URL = '/private/api/auth.php';

	private $auto_auth = false;
	private $session = [
		'id' => null,
		'modified_at' => 0
	];
	
	/**
	 * Constructor
	 * @param array $account
	 */
	private function __construct(Array $account)
	{
		$this->_account = $account;
		$this->full_domain = $account['zone'] == 'com' ? $account['domain'].'.kommo.com' : $account['domain'].'.amocrm.ru';
	}
	
	/**
	 * Get account auth data
	 * @param string|null $key
	 * @return array
	 */
	public function getAuth($key = null)
	{
		if (!is_null($key) && isset($this->_account[$key])) {
			return $this->_account[$key];
		}
		return $this->_account;
	}

	/**
	 * Create auth session
	 * @return Amoapi
	 */
	public function authorize()
	{
		$this->session['id'] = null;
		$this->session['modified_at'] = 0;
		$query = new Api\Query($this);
		$query->setUrl(self::AUTH_URL)
			  ->setRetry(false)
			  ->resetArgs()
			  ->setArgs([
				'USER_LOGIN' => $this->getAuth('login'),
				'USER_HASH' => $this->getAuth('hash'),
				'type' => 'json'
			  ]);
		$query->execute();
		if (!$data = $query->response->parseJson()) {
			throw new \Exception('Auth failed with invalid response data: '.$query->response->getData(), $query->response->getCode());
		}
		if (!isset($data->response->auth)) {
			throw new \Exception('Auth failed');
		}
		if (!$data->response->auth) {
			if (isset($data->response->error_code) && isset($data->response->error)) {
				throw new \Exception($data->response->error_code.': '.$data->response->error, $query->response->getCode());
			}
			throw new \Exception('Auth failed');
		}
		if ($query->response->getCode() != 200) {
			throw new \Exception('Auth failed with response code: '.$query->response->getCode(), $query->response->getCode());
		}
		return $this;
	}

	/**
	 * Set auto authorize
	 * @param bool $value
	 * @return bool
	 */
	public function autoAuth($value = true)
	{
		return $this->auto_auth = (bool)$value;
	}

	/**
	 * Has auto authorize
	 * @return bool
	 */
	public function hasAutoAuth()
	{
		return $this->auto_auth;
	}

	/**
	 * Set session
	 * @param string $id
	 * @param integer $modified
	 * @return Amoapi
	 */
	public function setSession($id, $modified)
	{
		$this->session['id'] = $id;
		$this->session['modified_at'] = $modified;
		return $this;
	}

	/**
	 * Has session exists
	 * @return bool
	 */
	public function hasSession()
	{
		$seconds = time()-$this->session['modified_at'];
		return !is_null($this->session['id']) && $seconds < self::SESS_LIFETIME;
	}

	/**
	 * Set account instance
	 * @param array $data
	 * @return Amoapi
	 */
	public static function setInstance(Array $data)
	{
		if (empty($data)) {
			throw new \Exception('Incorrect amoCRM account data');
		}
		if (empty($data['zone'])) {
			$data['zone'] = 'ru';
		}
		if (empty($data['lang'])) {
			$data['lang'] = $data['zone'] == 'ru' ? 'ru' : 'en';
		}
		if (empty($data['timezone'])) {
			$data['timezone'] = 'Europe/Moscow';
		}
		$account = [
			'id' => '', 'domain' => '', 'login' => '', 'hash' => '', 'zone' => '', 'lang' => '', 'timezone' => ''
		];
		foreach ($account as $key=>$val) {
			if (!isset($data[$key])) {
				throw new \Exception('Incorrect account field: '.$key);
			}
			$account[$key] = $data[$key];
		}
		if (!isset(self::$_instances[$account['id']])) {
			self::$_instances[$account['id']] = new static($account);
		}
		$instance = self::getInstance($account['id']);
		self::$_queries[$account['domain'].$account['id']] = new Collections\QueryCollection();
		self::$_queries[$account['domain'].$account['id']]->boot($instance);
		return $instance;
	}

	/**
	 * Get account instance
	 * @param integer $account_id
	 * @return Amoapi
	 */
	public static function getInstance($account_id)
	{
		if (!is_numeric($account_id)) {
			throw new \Exception('Account id must be numeric: '.$account_id);
		}
		if (!isset(self::$_instances[$account_id])) {
			throw new \Exception('Account not found: '.$account_id);
		}
		return self::$_instances[$account_id];
	}
	
	/**
	 * Call Service Methods
	 * @param string $service_name
	 * @param array $args
	 */
	public function __call($service_name, $args)
	{
		if (!in_array($service_name, $this->services)) {
			throw new \Exception('Invalid service called: '.$service_name);
		}
		$service_class = '\\Ufee\\Amo\\Services\\'.ucfirst($service_name);
		if (!$service = $service_class::getInstance($service_name, $this)) {
			$service = $service_class::setInstance($service_name, $this);
		}
		return $service;
	}
	
	/**
	 * Get Service
	 * @param string $target
	 */
	public function __get($target)
	{
		if ($target === 'queries') {
			return self::$_queries[$this->_account['domain'].$this->_account['id']];
		}
		if ($target === 'session') {
			return $this->session;
		}
		if (!in_array($target, $this->services)) {
			throw new \Exception('Invalid service called: '.$target);
		}
		$service_class = '\\Ufee\\Amo\\Services\\'.ucfirst($target);
		if (!$service = $service_class::getInstance($target, $this)) {
			$service = $service_class::setInstance($target, $this);
		}
		if (!method_exists($service, $target)) {
			throw new \Exception('Invalid service method called: '.$target.'()');
		}
		return $service->{$target}();
	}
}
