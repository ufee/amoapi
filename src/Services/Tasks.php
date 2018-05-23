<?php
/**
 * amoCRM API client Tasks service
 */
namespace Ufee\Amo\Services;

class Tasks extends \Ufee\Amo\Base\Services\LimitedList
{
	protected static 
		$_require = [
			'add' => ['element_id','element_type','text'],
			'update' => ['id', 'updated_at']
		];
	protected
		$entity_key = 'tasks',
		$entity_model = '\Ufee\Amo\Models\Task',
		$entity_collection = '\Ufee\Amo\Collections\TaskCollection',
		$cache_time = false;

    /**
     * Get full
	 * @return Collection
     */
	public function tasks()
	{
		return $this->list->recursiveCall();
	}
}
