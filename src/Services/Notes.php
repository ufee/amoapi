<?php
/**
 * amoCRM API client Notes service
 */
namespace Ufee\Amo\Services;

class Notes extends \Ufee\Amo\Base\Services\LimitedList
{
	protected static 
		$_require = [
			'add' => ['element_id','element_type','note_type','text'],
			'update' => ['id', 'updated_at']
		];
	protected
		$entity_key = 'notes',
		$entity_model = '\Ufee\Amo\Models\Note',
		$entity_collection = '\Ufee\Amo\Collections\NoteCollection',
		$cache_time = false;
	
    /**
     * Get notes by type
	 * @param integer $id
	 * @return Collection
     */
	public function type($id)
	{
		return $this->list->where('type', $id)
						  ->recursiveCall();
	}

    /**
     * Get full
	 * @return Collection
     */
	public function notes()
	{
		return $this->list->where('type', 4)
					->recursiveCall();
	}
}
