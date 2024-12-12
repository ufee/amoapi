<?php
/**
 * amoCRM API client Oauth handler - long token
 */
namespace Ufee\Amo\Base\Storage\Oauth;
use Ufee\Amo\Oauthapi;

class LongTokenStorage extends AbstractStorage
{

    public function __construct(array $options)
	{
		parent::__construct($options);
		
		if (empty($this->options['long_token'])) {
			throw new \Exception('Long Token Storage options[long_token] must be string of the access token');
		}
        if (empty($this->options['expires_in'])) {
			throw new \Exception('Long Token Storage options[expires_in] must be string|int that specifies the expiration date of the access token');
		}
	}

    public function initClient(Oauthapi $client)
    {
        parent::initClient($client);

        $key = $client->getAuth('domain').'_'.$client->getAuth('client_id');
		static::$_oauth[$key] = [
			"token_type" => "Bearer",
            "expires_in" => $this->options['expires_in'],
            "access_token" => $this->options['long_token'],
            "created_at" => 1
		];
    }
}