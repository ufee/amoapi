<?php
/**
 * amoCRM API client Companies service
 */
namespace Ufee\Amo\Services;
use Ufee\Amo\Base\Services\Traits;

class Companies extends \Ufee\Amo\Base\Services\MainEntity
{
	use Traits\SearchByName, Traits\SearchByPhone, Traits\SearchByEmail;

	protected static 
		$_require = [
			'add' => ['name'],
			'update' => ['id', 'updated_at']
		];
	protected
		$entity_key = 'companies',
		$entity_model = '\Ufee\Amo\Models\Company',
		$entity_collection = '\Ufee\Amo\Collections\CompanyCollection',
		$cache_time = false;
	
    /**
     * Get full
	 * @return Collection
     */
	public function companies()
	{
		return $this->list->recursiveCall();
	}
}
