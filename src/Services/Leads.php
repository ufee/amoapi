<?php
/**
 * amoCRM API client Leads service
 */
namespace Ufee\Amo\Services;

class Leads extends \Ufee\Amo\Base\Services\MainEntity
{
	protected static 
		$_require = [
			'add' => ['name'],
			'update' => ['id', 'updated_at']
		];
	protected
		$entity_key = 'leads',
		$entity_model = '\Ufee\Amo\Models\Lead',
		$entity_collection = '\Ufee\Amo\Collections\LeadCollection',
		$cache_time = false;

    /**
     * Get lead by term
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
	public function leads()
	{
		return $this->list->recursiveCall();
	}
}
