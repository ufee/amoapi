<?php
/**
 * amoCRM API client by oauth
 * @author Vlad Ionov <vlad@f5.com.ru>
 * @version 0.9
 */
namespace Ufee\Amo;
use Ufee\Amo\Base\Storage\Oauth;

class Oauthapi extends ApiClient
{
	const VERSION = 9;
	
	private $_token_refresh_callback;
	private static $_oauthStorage;

    /**
     * Constructor
	 * @param array $account
     */
    private function __construct(Array $account)
    {
		$this->_account = $account;
		
		if (!static::$_oauthStorage) {
			static::setOauthStorage(
				new Oauth\FileStorage(['path' => AMOAPI_ROOT.'/Cache'])
			);
		}
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
		return $this->getOauthStorage()->getOauthData($this, $key);
	}
	
    /**
     * Set oauth data
	 * @param array $oauth
	 * @return bool
     */
    public function setOauth(array $oauth)
    {
		return $this->getOauthStorage()->setOauthData($this, $oauth);
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
		$oauth['created_at'] = time();
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
		$oauth['created_at'] = time();
		
        if (is_callable($this->_token_refresh_callback)) {
            call_user_func($this->_token_refresh_callback, $oauth);
        }
		$this->setOauth($oauth);
		return $oauth;
	}
	
    /**
     * On access token refresh callback
	 * @param callable $callback
	 * @return Oauthapi
     */
    public function onAccessTokenRefresh(callable $callback)
    {
		$this->_token_refresh_callback = $callback;
		return $this;
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
     * Set oauth cache path - deprecated
	 * @param string $path
     * @return Oauthapi
     */
    public function setOauthPath($path)
    {
		static::setOauthStorage(
			new Oauth\FileStorage(['path' => $path])
		);
		return $this;
	}
	
	/**
     * Get oauth storage handler
	 * @return AbstractStorage
     */
    public static function getOauthStorage()
    {
		return static::$_oauthStorage;
	}
	
	/**
     * Set oauth storage handler
	 * @param AbstractStorage $storage
	 * @return void
     */
    public static function setOauthStorage(Oauth\AbstractStorage $storage)
    {
		static::$_oauthStorage = $storage;
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
