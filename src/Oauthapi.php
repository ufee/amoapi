<?php
/**
 * amoCRM API client by oauth
 * @author Vlad Ionov <vlad@f5.com.ru>
 * @version 0.9
 */
namespace Ufee\Amo;

class Oauthapi extends ApiClient
{
	const VERSION = 9;
	
	private $oauth_path = '/Cache';
	private $_oauth = null;

    /**
     * Constructor
	 * @param array $account
     */
    private function __construct(Array $account)
    {
		$this->_account = $account;
		$this->setOauthPath(AMOAPI_ROOT.$this->oauth_path);
    }

    /**
     * Get account data
	 * @param string|null $key
	 * @return string|array
     */
    public function getAuth($key = null)
    {
		if ($key == 'id') {
			return $this->_account['client_id'];
		}
		if (!is_null($key) && isset($this->_account[$key])) {
			return $this->_account[$key];
		}
		return $this->_account;
	}

    /**
     * Get oauth access data
	 * @param string|null $key
	 * @return string|array
     */
    public function getOauth($key = null)
    {
		if (is_null($this->_oauth)) {
			$this->initOauth();
		}
		if (!is_null($key) && array_key_exists($key, $this->_oauth)) {
			return $this->_oauth[$key];
		}
		return $this->_oauth;
	}
	
    /**
     * Set oauth data
	 * @param array $oauth
	 * @return bool
     */
    public function setOauth(array $oauth)
    {
		$oauth['created_at'] = time();
		$this->_oauth = $oauth;
        return file_put_contents($this->oauth_path.'/'.$this->getAuth('client_id').'.json', json_encode($oauth));
    }
	
    /**
     * Init oauth data
     */
    private function initOauth()
    {
		$this->_oauth = [
			'token_type' => '',
			'expires_in' => 0,
			'created_at' => 0,
			'access_token' => '',
			'refresh_token' => ''
		];
		if (file_exists($this->oauth_path.'/'.$this->getAuth('client_id').'.json')) {
			$this->_oauth = json_decode(file_get_contents($this->oauth_path.'/'.$this->getAuth('client_id').'.json'), true);
		}
	}
	
    /**
     * Set oauth cache path
	 * @param string $val
     * @return Oauthapi
     */
    public function setOauthPath($val)
    {
		$this->oauth_path = $val.'/'.$this->getAuth('domain');
        if (!file_exists($this->oauth_path)) {
            mkdir($this->oauth_path, 0777, true);
        }
        return $this;
	}
	
    /**
     * Get access token by code
	 * @param string $code
	 * @return array
     */
    public function fetchAccessToken($code)
    {
		$query = new \Ufee\Amo\Api\Oauth\Query($this);
		$query->setUrl('/oauth2/access_token')
			  ->setPostData([
				'client_id' => $this->getAuth('client_id'),
				'client_secret' => $this->getAuth('client_secret'),
				'redirect_uri' => $this->getAuth('redirect_uri'),
				'grant_type' => 'authorization_code',
				'code' => $code
			  ]);
		$response = new \Ufee\Amo\Api\Response($query->post(), $query);
		if (!$data = $response->parseJson()) {
			throw new \Exception('Fetch access token failed (non JSON), code: '.$response->getCode(), $response->getCode());
		}
		if ($response->getCode() != 200 && !empty($data->hint)) {
			throw new \Exception('Fetch access token error: '.$data->hint, $response->getCode());
		}
		$oauth = (array)$data;
		$this->setOauth($oauth);
		return $oauth;
	}
	
    /**
     * Get access token by refresh token
	 * @param string|null $refresh_token
	 * @return array
     */
    public function refreshAccessToken($refresh_token = null)
    {
		if (is_null($refresh_token)) {
			$refresh_token = $this->getOauth('refresh_token');
		}
		if (!$refresh_token) {
			throw new \Exception('Empty oauth refresh_token');
		}
		$query = new \Ufee\Amo\Api\Oauth\Query($this);
		$query->setUrl('/oauth2/access_token')
			  ->setPostData([
				'client_id' => $this->getAuth('client_id'),
				'client_secret' => $this->getAuth('client_secret'),
				'redirect_uri' => $this->getAuth('redirect_uri'),
				'grant_type' => 'refresh_token',
				'refresh_token' => $refresh_token
			  ]);
		$response = new \Ufee\Amo\Api\Response($query->post(), $query);
		if (!$data = $response->parseJson()) {
			throw new \Exception('Refresh access token failed (non JSON), code: '.$response->getCode(), $response->getCode());
		}
		if ($response->getCode() != 200 && !empty($data->hint)) {
			throw new \Exception('Refresh access token error: '.$data->hint, $response->getCode());
		}
		$oauth = (array)$data;
		$this->setOauth($oauth);
		return $oauth;
	}

    /**
     * Get authorize url
	 * @param array $data
	 * @return string
     */
    public function getOauthUrl(array $data = [])
    {
		$arg = [
			'mode' => 'popup',
			'state' => 'amoapi'
		];
		foreach ($arg as $key=>$val) {
			$arg[$key] = isset($data[$key]) ? $data[$key] : $val;
		}
		return 'https://amocrm.'.$this->getAuth('zone').'/oauth?client_id='.$this->getAuth('client_id').'&mode='.$arg['mode'].'&state='.$arg['state'];
	}

	/**
     * Set account instance
	 * @param array $data
	 * @return Oauthapi
     */
    public static function setInstance(Array $data)
    {
        if (empty($data) || empty($data['client_id'])) {
            throw new \Exception('Incorrect amoCRM oauth data');
		}
		$account = [
			'domain' => '',
			'client_id' => '',
			'client_secret' => '',
			'redirect_uri' => '',
			'lang' => 'ru',
			'zone' => 'ru',
			'timezone' => 'Europe/Moscow'
		];
		foreach ($account as $key=>$val) {
			$account[$key] = isset($data[$key]) ? $data[$key] : $val;
		}
        if (!isset(self::$_instances[$account['client_id']])) {
			self::$_instances[$account['client_id']] = new static($account);
		}
		$instance = self::getInstance($account['client_id']);
		self::$_queries[$account['client_id']] = new Collections\QueryCollection();
		self::$_queries[$account['client_id']]->boot($instance);
        return $instance;
	}

    /**
     * Get account instance
	 * @param string $client_id
	 * @return Oauthapi
     */
    public static function getInstance($client_id)
    {
		if (!isset(self::$_instances[$client_id])) {
			throw new \Exception('Account not found: '.$client_id);
		}
		return self::$_instances[$client_id];
	}
}
