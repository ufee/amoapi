<?php
/**
 * amoCRM API client Customers service
 */
namespace Ufee\Amo\Services;

class Customers extends \Ufee\Amo\Base\Services\MainEntity
{
	protected
		$entity_key = 'customers',
		$entity_model = '\Ufee\Amo\Models\Customer',
		$entity_collection = '\Ufee\Amo\Collections\CustomerCollection',
		$cache_time = false;
	
    /**
     * Get full
	 * @return Collection
     */
	public function customers()
	{
		return $this->list->recursiveCall();
	}
}
