<?php
/**
 * amoCRM API client Catalog elements service
 */
namespace Ufee\Amo\Services;

class CatalogElements extends \Ufee\Amo\Base\Services\LimitedList
{
	protected
		$entity_key = 'catalog_elements',
		$entity_model = '\Ufee\Amo\Models\CatalogElement',
		$entity_collection = '\Ufee\Amo\Collections\CatalogElementCollection',
		$cache_time = false;
		
    /**
     * Get full
	 * @return Collection
     */
	public function catalogElements()
	{
		return $this->list->recursiveCall();
	}
}
