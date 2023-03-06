<?php
/**
 * amoCRM Service Collection class
 */
namespace Ufee\Amo\Collections;

class ServiceCollection extends CollectionWrapper
{
    /**
     * Constructor
	 * @param array $elements
	 * @param Service $service
     */
    public function __construct(Array $elements, \Ufee\Amo\Base\Services\Service &$service)
    {
        $this->collection = new \Ufee\Amo\Base\Collections\Collection($elements);
		$this->attributes['service'] = $service;
	}
	
    public function service()
    {
		return $this->attributes['service'];
	}
}
