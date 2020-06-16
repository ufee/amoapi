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
     * Set Note pinned
	 * @param integer $bot_id
	 * @param integer $entity_id
	 * @param integer $entity_type - 1 – контакт, 2- сделка, 3 – компания
	 * @param bool $state
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
		if ($query->response->getCode() != 200) {
			throw new \Exception('Invalid response code: '.$query->response->getCode(), $query->response->getCode());
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
	 * return mixed
     */
	public function post($url, array $data = [], array $args = [])
	{
		if ($this->instance instanceOf \Ufee\Amo\Oauthapi) {
			$query = new Api\Oauth\Query($this->instance);
		} else {
			$query = new Api\Query($this->instance);
		}
		$query->setHeader('X-Requested-With', 'XMLHttpRequest')
			  ->setUrl($url)
			  ->setMethod('POST')
			  ->setPostData($data)
			  ->setArgs($args)
			  ->execute();
		if ($query->response->getCode() != 200) {
			throw new \Exception('Invalid response code: '.$query->response->getCode(), $query->response->getCode());
		}
		if ($data = $query->response->parseJson()) {
			return $data;
		}
		return $query->response->getData();
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
		if ($query->response->getCode() != 200) {
			throw new \Exception('Invalid response code: '.$query->response->getCode(), $query->response->getCode());
		}
		if ($data = $query->response->parseJson()) {
			return $data;
		}
		return $query->response->getData();
	}
}
