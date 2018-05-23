<?php
/**
 * amoCRM Model Custom field Collection class
 */
namespace Ufee\Amo\Collections;

class EntityCustomFieldCollection
{
    protected 
        $attributes = [];

    /**
     * Constructor
	 * @param array $elements
	 * @param Model $model
     */
    public function __construct(\Ufee\Amo\Models\EntityCustomFields &$fields, \Ufee\Amo\Base\Models\Model &$model)
    {
		$this->attributes['fields'] = $fields;
		$this->attributes['model'] = $model;
	}

    /**
     * Call Collection Methods
	 * @param string $service_name
     * @param array $args
     */
	public function __call($method, $args)
	{
		if (method_exists($this->fields, $method)) {
            return call_user_func_array(
                [$this->fields, $method], $args
            );
        }
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
