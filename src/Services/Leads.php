<?php
/**
 * amoCRM API client Leads service
 */
namespace Ufee\Amo\Services;
use Ufee\Amo\Base\Services\Traits;

class Leads extends \Ufee\Amo\Base\Services\MainEntity
{
	use Traits\SearchByName;
	
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
     * Service on load
	 * @return void
     */
	protected function _boot()
	{
		parent::_boot();
		$this->api_args['with'] = 'loss_reason_name,catalog_elements_links';
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
