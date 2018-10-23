<?php
/**
 * amoCRM API client Base service
 */
namespace Ufee\Amo\Base\Services;
use Ufee\Amo\Base\Services\Traits;
use Ufee\Amo\Base\Collections\Collection;

class MainEntity extends LimitedList
{
	use Traits\SearchByCustomField;
	const PHONE_RU_MOB = 'ru_mob';

	protected $methods = [
		'list', 'add', 'update', 'unlink'
	];
	
    /**
     * Get entitys by query
	 * @param string $query
	 * @return Collection
     */
	public function search($query)
	{
		return $this->list->where('query', $query)->recursiveCall();
	}
}
