<?php
/**
 * amoCRM API client Catalog elements service
 */
namespace Ufee\Amo\Services;

use Ufee\Amo\Base\Services\Traits\SearchByCustomField;

class CatalogElements extends \Ufee\Amo\Base\Services\LimitedList
{
    use SearchByCustomField;
    
	protected static 
		$_require = [
			'add' => ['catalog_id', 'name'],
			'update' => ['id', 'name', 'catalog_id', 'updated_at']
		];
	protected
		$entity_key = 'catalog_elements',
		$entity_model = '\Ufee\Amo\Models\CatalogElement',
		$entity_collection = '\Ufee\Amo\Collections\CatalogElementCollection',
		$cache_time = false,
		$methods = [
			'list', 'add', 'update', 'delete'
		];
		
    /**
     * Get full
	 * @return Collection
     */
	public function catalogElements()
	{
		return $this->list->recursiveCall();
	}
}
