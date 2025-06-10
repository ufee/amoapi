<?php
/**
 * amoCRM API client Ajax service
 */
namespace Ufee\Amo\Services;
use Ufee\Amo\Api;

class Ajax extends \Ufee\Amo\Base\Services\Service
{
    /**
     * Service on load
	 * @return void
     */
	protected function _boot()
	{
		
	}
	
    /**
     * Exchange api key to oauth
	 * @param integer $bot_id
	 * @param integer $entity_id
	 * @param integer $entity_type - 1 – контакт, 2 - сделка, 3 – компания
	 * @param bool $state
	 * return integer
     */
	public function exchangeApiKey($login, $api_key, $client_id, $client_secret)
	{
		if ($this->instance instanceOf \Ufee\Amo\Oauthapi) {
			$query = new Api\Oauth\Query($this->instance);
		} else {
			if (!$this->instance->hasSession() && !$this->instance->hasAutoAuth()) {
				$this->instance->authorize();
			}
			$query = new Api\Query($this->instance);
		}
		$req = [
			'login' => $login,
			'api_key' => $api_key,
			'client_uuid' => $client_id,
			'client_secret' => $client_secret
		];
		$query->setUrl('/oauth2/exchange_api_key')
			  ->setMethod('POST')
			  ->setJsonData($req)
			  ->execute();
		return $query->response->getCode();
	}

    /**
     * Run Salesbot
	 * @param integer $bot_id
	 * @param integer $entity_id
	 * @param integer $entity_type - 1 – контакт, 2 - сделка, 3 – компания
	 * return bool
     */
	public function runSalesbot($bot_id, $entity_id, $entity_type)
	{
		if ($this->instance instanceOf \Ufee\Amo\Oauthapi) {
			$query = new Api\Oauth\Query($this->instance);
		} else {
			if (!$this->instance->hasSession() && !$this->instance->hasAutoAuth()) {
				$this->instance->authorize();
			}
			$query = new Api\Query($this->instance);
		}
		$bot = [
			'bot_id' => $bot_id,
			'entity_id' => $entity_id,
			'entity_type' => $entity_type
		];
		$query->setUrl('/api/v2/salesbot/run')
			  ->setMethod('POST')
			  ->setPostData([$bot])
			  ->execute();
		if ($query->response->getCode() == 202) {
			return true;
		}
		return false;
	}

    /**
     * Set Note pinned
	 * @param integer $note_id
	 * @param bool $state
	 * return bool
     */
	public function setNotePinned($note_id, $state = true)
	{
		if ($this->instance instanceOf \Ufee\Amo\Amoapi && !$this->instance->hasSession() && !$this->instance->hasAutoAuth()) {
			$this->instance->authorize();
		}
		$result = $this->patch('/v3/notes/'.$note_id, [
			'pinned' => (bool)$state
		]);
		if (!empty($result->_embedded) && isset($result->_embedded->items[0]) && $result->_embedded->items[0]->id === $note_id) {
			return true;
		}
		return false;
	}

    /**
     * Ajax GET attachment
	 * @param string $attachment filename
	 * return string
     */
	public function getAttachment($attachment)
	{
		if ($this->instance instanceOf \Ufee\Amo\Oauthapi) {
			$query = new Api\Oauth\Query($this->instance);
		} else {
			if (!$this->instance->hasSession() && !$this->instance->hasAutoAuth()) {
				$this->instance->authorize();
			}
			$query = new Api\Query($this->instance);
		}
		$query->setUrl('/download/'.$attachment)
			  ->resetArgs()
			  ->execute();
		if ($query->response->getCode() != 200) {
			if ($query->response->getCode() == 404) {
				throw new \Exception('Attachment not found: '.$attachment, $query->response->getCode());
			}
			throw new \Exception('Invalid response code: '.$query->response->getCode(), $query->response->getCode());
		}
		return $query->response->getData();
	}

	/**
     * Ajax unlink entity request
	 * @param string $entity - Тип главной сущности: leads|contacts|companies|customers
	 * @param array $data - Массив данных
	 * @param int $data[]['entity_id'] ID главной сущности
	 * @param int $data[]['to_entity_id'] ID связанной сущности
	 * @param string $data[]['to_entity_type'] Тип связанной сущности: leads, contacts, companies, customers, catalog_elements
	 * @param int $data[]['metadata']['catalog_id'] ID каталога
	 * @param int $data[]['metadata']['updated_by'] ID пользователя, от имени которого осуществляется открепление
	 * 
	 * 
	 * return mixed
     */
	public function unlinkEntity($entity, array $data)
	{
		if ($this->instance instanceOf \Ufee\Amo\Oauthapi) {
			$query = new Api\Oauth\Query($this->instance);
		} else {
			$query = new Api\Query($this->instance);
		}
		$query->setHeader('X-Requested-With', 'XMLHttpRequest')
			  ->setUrl("/api/v4/".$entity."/unlink")
			  ->setMethod('POST');

		$query->setJsonData($data);
		
		$query->execute();

		$code = $query->response->getCode();
		if (!in_array($code, [204])) {
			throw new \Exception('Invalid response code: '.$code.', body: '.print_r($query->response->getData(),1), $code);
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
     * Ajax link entity request
	 * @param string $entity - Тип главной сущности: leads|contacts|companies|customers
	 * @param array $data - Массив данных
	 * @param int $data[]['entity_id'] ID главной сущности
	 * @param int $data[]['to_entity_id'] ID связанной сущности
	 * @param string $data[]['to_entity_type'] Тип связанной сущности: leads, contacts, companies, customers, catalog_elements
	 * @param int $data[]['metadata']['catalog_id'] ID каталога
	 * @param int|float $data[]['metadata']['quantity'] Количество прикрепленных элементов каталогов
	 * @param bool $data[]['metadata']['is_main'] Является ли контакт главным
	 * @param int $data[]['metadata']['updated_by'] ID пользователя, от имени которого осуществляется открепление
	 * @param int|null $data[]['metadata']['price_id'] ID поля типа Цена, которое будет установлено для привязанного элемента в контексте сущности
	 * 
	 * 
	 * return mixed
     */
	public function linkEntity($entity, array $data)
	{
		if ($this->instance instanceOf \Ufee\Amo\Oauthapi) {
			$query = new Api\Oauth\Query($this->instance);
		} else {
			$query = new Api\Query($this->instance);
		}
		$query->setHeader('X-Requested-With', 'XMLHttpRequest')
			  ->setUrl("/api/v4/".$entity."/link")
			  ->setMethod('POST');

		$query->setJsonData($data);
		
		$query->execute();

		$code = $query->response->getCode();
		if (!in_array($code, [200])) {
			throw new \Exception('Invalid response code: '.$code.', body: '.print_r($query->response->getData(),1), $code);
		}
		if ($data = $query->response->parseJson()) {
			return $data;
		}
		return $query->response->getData();
	}
	
    /**
     * Ajax GET request
	 * @param string $url
	 * @param array $args
	 * return mixed
     */
	public function get($url, array $args = [])
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
     * Ajax POST request
	 * @param string $url
	 * @param array $data
	 * @param array $args
	 * @param string $post_type - raw|json
	 * return mixed
     */
	public function post($url, array $data = [], array $args = [], $post_type = 'raw')
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
     * Ajax POST json request
	 * @param string $url
	 * @param array $data
	 * @param array $args
	 * return mixed
     */
	public function postJson($url, array $data = [], array $args = [])
	{
		return $this->post($url, $data, $args, 'json');
	}

    /**
     * Ajax PATCH request
	 * @param string $url
	 * @param array $data
	 * @param array $args
	 * return mixed
     */
	public function patch($url, array $data = [], array $args = [])
	{
		if ($this->instance instanceOf \Ufee\Amo\Oauthapi) {
			$query = new Api\Oauth\Query($this->instance);
		} else {
			$query = new Api\Query($this->instance);
		}
		$query->setUrl($url)
			  ->setMethod('PATCH')
			  ->setJsonData($data)
			  ->setArgs($args)
			  ->execute();
		$code = $query->response->getCode();
		if (!in_array($code, [200, 202])) {
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
}
