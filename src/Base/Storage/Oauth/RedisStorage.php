<?php
/**
 * amoCRM API client Oauth handler - Redis
 */
namespace Ufee\Amo\Base\Storage\Oauth;
use Ufee\Amo\Oauthapi;

class RedisStorage extends AbstractStorage
{
	const OAUTH_TTL = 7776000;
	
    /**
     * Constructor
	 * @param array $options
     */
	public function __construct(array $options)
	{
		parent::__construct($options);
		
		if (empty($this->options['connection']) || !$this->options['connection'] instanceOf \Redis) {
			throw new \Exception('Redis Storage options[connection] must be instance of \Redis');
		}
	}
	
    /**
     * Init oauth handler
	 * @param Oauthapi $client
	 * @return void
     */
	protected function initClient(Oauthapi $client)
	{
		parent::initClient($client);
		
		$key = $client->getAuth('domain').'_'.$client->getAuth('client_id');
		if ($data = $this->options['connection']->get($key)) {
			static::$_oauth[$key] = $data;
		}
	}
	
    /**
     * Set oauth data
	 * @param Oauthapi $client
	 * @param array $oauth
	 * @return void
     */
	public function setOauthData(Oauthapi $client, array $oauth)
	{
		parent::setOauthData($client, $oauth);
		return $this->options['connection']->setEx($client->getAuth('domain').'_'.$client->getAuth('client_id'), static::OAUTH_TTL, $oauth);
	}
}
