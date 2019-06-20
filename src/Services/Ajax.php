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
     * Ajax GET attachment
	 * @param string $attachment filename
	 * return string
     */
	public function getAattachment($attachment)
	{
		if (!$this->instance->hasSession() && !$this->instance->hasAutoAuth()) {
			$this->instance->authorize();
		}
		$query = new Api\Query($this->instance);
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
		$query = new Api\Query($this->instance);
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
		$query = new Api\Query($this->instance);
		$query->setHeader('X-Requested-With', 'XMLHttpRequest')
			  ->setUrl($url)
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
}
