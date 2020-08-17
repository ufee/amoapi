<?php
/**
 * amoCRM API client Oauth handler - files
 */
namespace Ufee\Amo\Base\Storage\Oauth;
use Ufee\Amo\Oauthapi;

class FileStorage extends AbstractStorage
{
    /**
     * Constructor
	 * @param array $options
     */
	public function __construct(array $options)
	{
		parent::__construct($options);
		
		if (empty($this->options['path'])) {
			throw new \Exception('File Storage options[path] must be string path');
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
		
		if (!file_exists($this->options['path'].'/'.$client->getAuth('domain'))) {
			mkdir($this->options['path'].'/'.$client->getAuth('domain'), 0777, true);
		}
		if (file_exists($this->options['path'].'/'.$client->getAuth('domain').'/'.$client->getAuth('client_id').'.json')) {
			$key = $client->getAuth('domain').'_'.$client->getAuth('client_id');
			static::$_oauth[$key] = json_decode(file_get_contents($this->options['path'].'/'.$client->getAuth('domain').'/'.$client->getAuth('client_id').'.json'), true);
		}
	}
	
    /**
     * Set oauth data
	 * @param Oauthapi $client
	 * @param array $oauth
	 * @return bool
     */
	public function setOauthData(Oauthapi $client, array $oauth)
	{
		parent::setOauthData($client, $oauth);
        return file_put_contents($this->options['path'].'/'.$client->getAuth('domain').'/'.$client->getAuth('client_id').'.json', json_encode($oauth));
	}
}
