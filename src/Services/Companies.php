<?php
/**
 * amoCRM API client Companies service
 */
namespace Ufee\Amo\Services;

class Companies extends \Ufee\Amo\Base\Services\MainEntity
{
	protected static 
		$_require = [
			'add' => ['name'],
			'update' => ['id', 'updated_at']
		];
	protected
		$entity_key = 'сompanies',
		$entity_model = '\Ufee\Amo\Models\Company',
		$entity_collection = '\Ufee\Amo\Collections\CompanyCollection',
		$cache_time = false;
	
    /**
     * Get companies by term
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
	public function companies()
	{
		return $this->list->recursiveCall();
	}
}
