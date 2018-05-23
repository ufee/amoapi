<?php
/**
 * amoCRM API client Contacts service
 */
namespace Ufee\Amo\Services;

class Contacts extends \Ufee\Amo\Base\Services\MainEntity
{
	protected static 
		$_require = [
			'add' => ['name'],
			'update' => ['id', 'updated_at']
		];
	protected
		$entity_key = 'contacts',
		$entity_model = '\Ufee\Amo\Models\Contact',
		$entity_collection = '\Ufee\Amo\Collections\ContactCollection',
		$cache_time = false;
	
    /**
     * Get contact by term
	 * @param string $query
	 * @return Collection
     */
	public function search($query)
	{
		return $this->list->where('query', $query)
						  ->recursiveCall();
	}
	
    /**
     * Get full
	 * @return Collection
     */
	public function contacts()
	{
		return $this->list->recursiveCall();
	}
}
