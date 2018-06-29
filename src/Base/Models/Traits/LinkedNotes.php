<?php
/**
 * amoCRM Base trait - linked models
 */
namespace Ufee\Amo\Base\Models\Traits;

trait LinkedNotes
{
	/**
     * Create linked note model
     * @return Note
     */
    public function createNote($type = 4)
    {
		$note = $this->service->instance->notes()->create();
		$note->note_type = $type;
		$note->element_type = static::$_type_id;
		$note->element_id = $this->id;
		
		if (!is_null($this->attributes['notes'])) {
			$this->attributes['notes']->push($note);
		}
		return $note;
	}
	
	/**
     * Linked notes get method
     * @return NotesList
     */
    public function notes($type = null)
    {
		$service = $this->service->instance->notes()->where('type', static::$_type)->where('element_id', $this->id);
		if (!is_null($type)) {
			$service->where('note_type', $service);
		}
		return $service;
	}
	
    /**
     * Protect notes access
	 * @param mixed $notes attribute
	 * @return NoteCollection
     */
    protected function notes_access($notes)
    {
		if (is_null($this->attributes['notes'])) {
			$this->attributes['notes'] = $this->notes()->recursiveCall();
		}
		return $this->attributes['notes'];
	}
}
