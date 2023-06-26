<?php
/**
 * amoCRM API client Salesbots service
 */
namespace Ufee\Amo\Services;
use Ufee\Amo\Base\Collections\Collection;
use Ufee\Amo\Api;

class Salesbots extends \Ufee\Amo\Base\Services\Service
{
    /**
     * Service on load
	 * @return void
     */
	protected function _boot()
	{
		
	}
	
    /**
     * Get Salesbots
	 * return Collection
     */
	public function salesbots()
	{
		return $this->get();
	}
	
    /**
     * Get Salesbots
	 * @param integer $page
	 * @param integer $limit
	 * return Collection
     */
	public function get(int $page = 1, int $limit = 250)
	{
		$resp = $this->getRequest('/api/v4/bots', [
			'page' => $page,
			'limit' => $limit
		]);
		if (!is_object($resp)) {
			return new Collection();
		}
		return new Collection($resp->_embedded->items);
	}


    /**
     * Start Salesbot
	 * @param integer $bot_id
	 * @param integer $entity_id
	 * @param integer $entity_type - 1 – контакт, 2 - сделка, 3 – компания
	 * return bool
     */
	public function start(int $bot_id, int $entity_id, int $entity_type = 2)
	{
		$bot = [
			'bot_id' => $bot_id,
			'entity_id' => $entity_id,
			'entity_type' => $entity_type
		];
		$result = $this->postRequest('/api/v2/salesbot/run', [$bot]);
		return $result;
	}
	
    /**
     * Stop Salesbot
	 * @param integer $bot_id
	 * @param integer $entity_id
	 * @param integer $entity_type - 1 – контакт, 2 - сделка, 3 – компания
	 * return bool
     */
	public function stop(int $bot_id, int $entity_id, int $entity_type)
	{
		$entitys = [1 => 'contacts', 2 => 'leads', 3 => 'companies'];
		$result = $this->postJson('/ajax/v4/bots/'.$bot_id.'/stop', [
			'entity_id' => $entity_id,
			'entity_type' => $entitys[$entity_type]
		]);
		return $result;
	}

    /**
     * GET request
	 * @param string $url
	 * @param array $args
	 * return mixed
     */
	public function getRequest($url, array $args = [])
	{
		if ($this->instance instanceOf \Ufee\Amo\Oauthapi) {
			$query = new Api\Oauth\Query($this->instance);
		} else {
			$query = new Api\Query($this->instance);
		}
		$query->setHeader('X-Requested-With', 'XMLHttpRequest')
			  ->setUrl($url)
			  ->setArgs($args)
			  ->execute();
		$code = $query->response->getCode();
		if (!in_array($code, [200, 204])) {
			throw new \Exception('Invalid response code: '.$code, $code);
		}
		if ($code == 204) {
			return true;
		}
		if ($data = $query->response->parseJson()) {
			return $data;
		}
		return $query->response->getData();
	}

    /**
     * POST request
	 * @param string $url
	 * @param array $data
	 * @param array $args
	 * @param string $post_type - raw|json
	 * return mixed
     */
	public function postRequest($url, array $data = [], array $args = [], $post_type = 'raw')
	{
		if ($this->instance instanceOf \Ufee\Amo\Oauthapi) {
			$query = new Api\Oauth\Query($this->instance);
		} else {
			$query = new Api\Query($this->instance);
		}
		$query->setHeader('X-Requested-With', 'XMLHttpRequest')
			  ->setUrl($url)
			  ->setMethod('POST')
			  ->setArgs($args);
		if ($post_type == 'json') {
			$query->setJsonData($data);
		} else {
			$query->setPostData($data);
		}
		$query->execute();
		$code = $query->response->getCode();
		if (!in_array($code, [200, 201, 202])) {
			throw new \Exception('Invalid response code: '.$code, $code);
		}
		if ($code == 202) {
			return true;
		}
		if ($data = $query->response->parseJson()) {
			return $data;
		}
		return $query->response->getData();
	}
	
    /**
     * POST json request
	 * @param string $url
	 * @param array $data
	 * @param array $args
	 * return mixed
     */
	public function postJson($url, array $data = [], array $args = [])
	{
		return $this->postRequest($url, $data, $args, 'json');
	}
}
