<?php
/**
 * amoCRM API client Base service method
 */
namespace Ufee\Amo\Base\Methods;
use Ufee\Amo\Api,
	Ufee\Amo\Base\Collections\Collection;

class Post extends Method
{
	protected
		$method = 'post';
	
    /**
     * Call api method
	 * @param array $arg
	 * @return Collection
     */
	public function call($post_data = [], $arg = [])
	{
		$query = new Api\Query($this->service->instance, get_class($this->service));
		$query->setUrl($this->url);
		$query->setMethod('POST');
		$query->setPostData($post_data);
		$query->setArgs(
			array_merge($this->service->api_args, $this->args, $arg)
		);
		//echo date('Y-m-d H:i:s').' - Api post query '.$query->getUrl()."\n";
		//print_r($post_data);
		$query->execute();
		return $this->parseResponse(
			$query
		);
	}
	
    /**
     * Parse api response
	 * @param Query $query
	 * @return Collection
     */
    protected function parseResponse(Api\Query &$query)
    {
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
		$result = new Collection();
		foreach ($json->_embedded->items as $raw) {
			$raw->{'query_hash'} = $query->generateHash();
			$result->push($raw);
		}
		return $result;
	}
}
