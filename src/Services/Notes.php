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

    /**
     * Get models by id
	 * @param integer|array $id
	 * @param integer $type note type
	 * @return Model|Collection
     */
	public function find($id, $type = 4)
	{
		$result = $this->list->where('limit_rows', is_array($id) ? count($id) : 1)
							 ->where('limit_offset', 0)
							 ->where('type', $type)
							 ->where('id', $id)
							 ->call();
		if (is_array($id)) {
			return $result;
		}
		if (!$model = $result->get(0)) {
			return null;
		}
		return $model;
	}
}
