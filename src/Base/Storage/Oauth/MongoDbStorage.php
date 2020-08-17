<?php
/**
 * amoCRM API client Oauth handler - MongoDB
 */
namespace Ufee\Amo\Base\Storage\Oauth;
use Ufee\Amo\Oauthapi;

class MongoDbStorage extends AbstractStorage
{
    /**
     * Constructor
	 * @param array $options
     */
	public function __construct(array $options)
	{
		parent::__construct($options);
		
		if (empty($this->options['collection']) || !$this->options['collection'] instanceOf \MongoDB\Collection) {
			throw new \Exception('MongoDB Storage options[collection] must be instance of \MongoDB\Collection');
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
		if ($row = $this->options['collection']->findOne(['_id' => $key])) {
			static::$_oauth[$key] = (array)$row->data;
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
		
		$result = $this->options['collection']->updateOne(
			['_id' => $client->getAuth('domain').'_'.$client->getAuth('client_id')],
			['$set' => ['data' => $oauth]], [
				'upsert' => true
			]
		);
		return ($result->getUpsertedCount() || $result->getMatchedCount());
	}
}
