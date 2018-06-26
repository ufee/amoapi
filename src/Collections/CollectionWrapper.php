<?php
/**
 * amoCRM Collection wrapper class
 */
namespace Ufee\Amo\Collections;

class CollectionWrapper
{
	protected
        $collection,
        $attributes = [];

    /**
     * Get collections
	 * @return Colection
     */
    public function collection()
    {
		return $this->collection;
    }
    
    /**
     * Merge collections
	 * @param Collection $collection
	 * @return Colection
     */
    public function merge(CollectionWrapper &$collection)
    {
        $collection_instance = $collection->collection();
		return $this->collection->merge($collection_instance);
	}

    /**
     * Call Collection Methods
	 * @param string $method
     * @param array $args
     */
	public function __call($method, $args)
	{
		if (method_exists($this->collection, $method)) {
            return call_user_func_array(
                [$this->collection, $method], $args
            );
        }
        throw new \Exception('Invalid '.static::class.' method: '.$method);
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
