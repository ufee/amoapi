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
	 * @param integer $note_type 4,...
	 * @param string $element_type - contact/lead/company/task
	 * @return Collection
     */
	public function type($note_type, $element_type = 'all')
	{
		return $this->list->where('note_type', $note_type)
						  ->where('type', $element_type)
						  ->recursiveCall();
	}

    /**
     * Get full
	 * @return Collection
     */
	public function notes()
	{
		return $this->list->where('type', 'all')
					->recursiveCall();
	}

    /**
     * Get models by id
	 * @param integer|array $id
	 * @param string $element_type - contact/lead/company/task
	 * @return Model|Collection
     */
	public function find($id, $element_type = 'lead')
	{
		$result = $this->list->where('limit_rows', is_array($id) ? count($id) : 1)
							 ->where('limit_offset', 0)
							 ->where('type', $element_type)
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
