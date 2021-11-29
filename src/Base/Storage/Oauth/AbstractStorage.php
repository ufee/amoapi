<?php
/**
 * amoCRM API client Oauth handler interface
 */
namespace Ufee\Amo\Base\Storage\Oauth;
use Ufee\Amo\Oauthapi;

class AbstractStorage
{
	protected $options = [];
	protected static $_oauth = [];
	
    /**
     * Constructor
	 * @param array $options
     */
	public function __construct(array $options)
	{
		$this->options = $options;
	}
	
    /**
     * Init oauth handler
	 * @param Oauthapi $client
	 * @return void
     */
	public function initClient(Oauthapi $client)
	{
		$key = $client->getAuth('domain').'_'.$client->getAuth('client_id');
		static::$_oauth[$key] = [
			'token_type' => '',
			'expires_in' => 0,
			'access_token' => '',
			'refresh_token' => '',
			'created_at' => 0
		];
	}
	
    /**
     * Set oauth data
	 * @param Oauthapi $client
	 * @param array $oauth
	 * @return bool
     */
	public function setOauthData(Oauthapi $client, array $oauth)
	{
		$key = $client->getAuth('domain').'_'.$client->getAuth('client_id');
		if (!array_key_exists($key, static::$_oauth)) {
			$this->initClient($client);
		}
		static::$_oauth[$key] = $oauth;
		return true;
	}
	
    /**
     * Get oauth data
	 * @param Oauthapi $client
	 * @param string|null $field
	 * @return array
     */
    public function getOauthData(Oauthapi $client, $field = null)
    {
		$key = $client->getAuth('domain').'_'.$client->getAuth('client_id');
		if (!array_key_exists($key, static::$_oauth)) {
			$this->initClient($client);
		}
		if (!is_null($field) && array_key_exists($field, static::$_oauth[$key])) {
			return static::$_oauth[$key][$field];
		}
		return static::$_oauth[$key];
	}
}
