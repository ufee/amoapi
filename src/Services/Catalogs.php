<?php
/**
 * amoCRM API client Catalogs service
 */
namespace Ufee\Amo\Services;

class Catalogs extends \Ufee\Amo\Base\Services\LimitedList
{
	protected
		$entity_key = 'catalogs',
		$entity_model = '\Ufee\Amo\Models\Catalog',
		$entity_collection = '\Ufee\Amo\Collections\CatalogCollection',
		$cache_time = false;
	
    /**
     * Get full
	 * @return Collection
     */
	public function catalogs()
	{
		return $this->list->recursiveCall();
	}
}
