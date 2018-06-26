<?php
/**
 * amoCRM API client Contacts service
 */
namespace Ufee\Amo\Services;
use Ufee\Amo\Base\Services\Traits;

class Contacts extends \Ufee\Amo\Base\Services\MainEntity
{
	use Traits\SearchByName, Traits\SearchByPhone, Traits\SearchByEmail;

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
     * Get full
	 * @return Collection
     */
	public function contacts()
	{
		return $this->list->recursiveCall();
	}
}
