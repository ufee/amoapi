<?php
/**
 * amoCRM API client GET service method
 */
namespace Ufee\Amo\Base\Methods;
use Ufee\Amo\Api;

class Get extends Method
{
	protected
		$method = 'get';
	
    /**
     * Request arg set
	 * @param string $key
	 * @param mixed $value
     */
    public function where($key, $value = null)
    {
		if (is_array($key)) {
			foreach ($key as $k=>$v) {
				$this->args[$k] = $v;
			}
		} else {
			$this->args[$key] = $value;
		}
		return $this;
	}
	
    /**
     * Call api method
	 * @param array $arg
	 * @return Collection
     */
	public function call($arg = [])
	{
		$query = new Api\Query($this->service->instance, get_class($this->service));
		$query->setUrl($this->url);
		$query->setArgs(
			array_merge($this->service->api_args, $this->args, $arg)
		);
		if ($this->service->modified_from) {
			$d = \DateTime::createFromFormat('U', $this->service->modified_from, new \DateTimeZone($this->service->instance->getAuth('timezone')));
			$d->setTimezone(new \DateTimeZone("UTC"));
			$query->setHeader(
				'If-Modified-Since', $d->format('D, d M Y H:i:s T')
			);
		}
		if ($this->service->canCache()) {
			if ($cached = $this->service->queries->getCached($query->generateHash(), $this->service->cache_time)) {
				return $this->parseResponse(
					$cached->response
				);
			}
		}
		$query->execute();
		if ($query->response->getCode() == 429) {
			sleep(1);
			$query->execute();
		}
		return $this->parseResponse(
			$query
		);
	}
	
    /**
     * Parse api response
	 * @param Query $query
	 * @return Collection
     */
    protected function parseResponse(\Ufee\Amo\Api\Query &$query)
    {
		$collection_class = $this->service->entity_collection;
		$collection = new $collection_class([], $this->service);
		$model_class = $this->service->entity_model;

		if ($query->response->getCode() == 204) {
			return $collection;
		}
		if (!$json = $query->response->parseJson()) {
			throw new \Exception('Invalid API response (non JSON), code: '.$query->response->getCode());
		}
		if (!isset($json->_embedded->items)) {
			if (isset($json->error)) {
				throw new \Exception('API response error (code: '.$query->response->getCode().') '.$json->error);
			}
			if (isset($json->title) && isset($json->detail)) {
				throw new \Exception('API response error (code: '.$query->response->getCode().'), '.$json->detail);
			}
			throw new \Exception('Invalid API response ('.$this->service->entity_key.': items not found), code: '.$query->response->getCode());
		}
		foreach ($json->_embedded->items as $raw) {
			$collection->push(
				new $model_class($raw, $this->service, $query)
			);
		}
		return $collection;
	}
}
