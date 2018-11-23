<?php
/**
 * amoCRM API model
 */
namespace Ufee\Amo\Base\Models;
use Ufee\Amo\Base\Models\Traits;

class ApiModel extends Model
{
	use Traits\LinkedUsers;

	protected static
		$_type = '',
		$_type_id = 0;
	protected 
		$system = [
			'id',
			'account_id',
			'request_id'
		],
		$hidden = [
			'query_hash',
			'service',
			'createdUser',
			'responsibleUser',
			'updatedUser'
		],
		$writable = [
			'created_by',
			'responsible_user_id',
			'updated_at'
		],
		$attributes = [],
		$_onCreate;
		
    /**
     * Constructor
	 * @param mixed $data
	 * @param Service $service
     */
    public function __construct($data, \Ufee\Amo\Base\Services\Service &$service, &$query = null)
    {
		if (!is_array($data) && !is_object($data)) {
			throw new \Exception(static::getBasename().' model data must be array or object');
		}
		$data = (object)$data;
        foreach ($this->system as $field_key) {
			$this->attributes[$field_key] = null;
		}
        foreach ($this->hidden as $field_key) {
			$this->attributes[$field_key] = null;
		}
        foreach ($this->writable as $field_key) {
			$this->attributes[$field_key] = null;
		}
        foreach ($data as $field_key=>$val) {
			if (array_key_exists($field_key, $this->attributes)) {
				$this->attributes[$field_key] = $val;
			}
		}
		$this->attributes['service'] = $service;
		
		if ($query instanceof \Ufee\Amo\Api\Query) {
			$this->attributes['query_hash'] = $query->hash;
		}
		$this->_onCreate = function() {};
		$this->_boot($data);
	}

	/**
     * Set Model id after create
	 * @param callable $callback
     */
    public function onCreate($callback)
    {
		if (is_callable($callback)) {
			$this->_onCreate = $callback;
		}
	}

	/**
     * Set Model id after create
	 * @param integer $id
     */
    public function setId($id)
    {
		$this->attributes['id'] = $id;
	}

	/**
     * Set API Query hash
	 * @param string $hash
     */
    public function setQueryHash($hash)
    {
		$this->attributes['query_hash'] = $hash;
	}
	
    /**
     * Get changed raw model api data
	 * @return array
     */
    public function getChangedRawApiData()
	{
		if ($this->hasAttribute('updated_at') && !$this->hasChanged('updated_at')) {
			$date = new \DateTime('now', new \DateTimeZone($this->service->instance->getAuth('timezone')));
			$this->updated_at = $date->getTimestamp();					
		}
		$data = $this->getChangedData();
		if ($this->hasAttribute('tags') && $this->hasChanged('tags') && empty($this->attributes['tags'])) {
			$data['tags'] = '';
		}
		return $data;
	}
		
    /**
     * Get raw model api data
	 * @return object
     */
    public function getResponse()
    {
		print_r($this->service->queries);
		if (!$query = $this->service->queries->find('hash', $this->query_hash)->first()) {
			return null;
		}
		if (!$parsed = $query->response->parseJson()) {
			return null;
		}
		if (!isset($parsed->_embedded) || !isset($parsed->_embedded->items)) {
			return null;
		}
		$items = new \Ufee\Amo\Base\Collections\Collection($parsed->_embedded->items);
		return $items->find('id', $this->id)->first();
	}

    /**
     * Save model in CRM
	 * @return bool
     */
    public function save()
    {
		if (!$this->id) {
			if ($this->service->add($this)) {
				$callback = $this->_onCreate;
				$callback($this);
				return true;
			}
			return false;
		}
		return $this->service->update($this);
	}
}
