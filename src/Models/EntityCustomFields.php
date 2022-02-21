<?php
/**
 * amoCRM Model Entity Custom field class
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Base\Collections as BaseCollections;

class EntityCustomFields
{
    protected $attributes = [];

    /**
     * Constructor
	 * @param array $elements
	 * @param Account $account
     */
    public function __construct(BaseCollections\Collection &$fields)
    {
        $this->attributes['fields'] = $fields;
    }

    /**
     * Get cf by name
     * @param string $name
	 * @return mixed
     */
    public function byName($cf_name)
    {
		return $this->fields->find('name', $cf_name)->first();
    }
    
    /**
     * Get cf by id
     * @param integer $cf_id
	 * @return mixed
     */
    public function byId($cf_id)
    {
		return $this->fields->find('id', $cf_id)->first();
    }
	
    /**
     * Get cf by code
     * @param string $cf_code
	 * @return mixed
     */
    public function byCode($cf_code)
    {
		return $this->fields->find('code', $cf_code)->first();
    }
	
    /**
     * Get cf by type id
     * @param string $cf_id
	 * @return Collection
     */
    public function byTypeId($cf_id)
    {
		return $this->fields->find('field_type', $cf_id);
    }
    
    /**
     * CF each
     * @param callable $callback
     */
    public function each($callback)
    {
		return $this->fields->each($callback);
    }

    /**
     * Get cf changed in raw for api
	 * @return array
     */
    public function getChangedApiRaw()
    {
        $raw = [];
        $this->fields->each(function($cfield) use(&$raw) {
            if ($cfield->hasChanged('values')) {
                $raw[]= $cfield->getApiRaw();
            }
        });
        return $raw;
    }
    
    /**
     * Protect get fields
	 * @param string $field
     */
	public function __get($field)
	{
		if (!array_key_exists($field, $this->attributes)) {
			throw new \Exception('Invalid '.static::class.' attribute: '.$field);
		}
		return $this->attributes[$field];
	}
}
