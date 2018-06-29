<?php
/**
 * amoCRM API client Customers service
 */
namespace Ufee\Amo\Services;

class Customers extends \Ufee\Amo\Base\Services\MainEntity
{
	protected static 
		$_require = [
			'add' => ['name', 'next_date'],
			'update' => ['id', 'updated_at']
		];
	protected
		$entity_key = 'customers',
		$entity_model = '\Ufee\Amo\Models\Customer',
		$entity_collection = '\Ufee\Amo\Collections\CustomerCollection',
		$cache_time = false,
		$methods = [
			'list', 'add', 'update', 'delete'
		];
	
    /**
     * Get full
	 * @return Collection
     */
	public function customers()
	{
		return $this->list->recursiveCall();
	}
}
