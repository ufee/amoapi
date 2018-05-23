<?php
/**
 * amoCRM Base trait - linked models
 */
namespace Ufee\Amo\Base\Models\Traits;

trait LinkedTasks
{
	/**
     * Create linked task model
     * @return Task
     */
    public function createTask($type = 1)
    {
		$task = $this->service->instance->tasks()->create();
		$task->task_type = $type;
		$task->element_type = static::$_type_id;
		$task->element_id = $this->id;
		
		if (!is_null($this->attributes['tasks'])) {
			$this->attributes['tasks']->push($task);
		}
		return $task;
	}
	
	/**
     * Linked tasks get method
     * @return TasksList
     */
    public function tasks()
    {
		return $this->service->instance->tasks()->where('type', static::$_type)->where('element_id', $this->id);
	}
	
    /**
     * Protect tasks access
	 * @param mixed $tasks attribute
	 * @return TaskCollection
     */
    protected function tasks_access($tasks)
    {
		if (is_null($this->attributes['tasks'])) {
			$this->attributes['tasks'] = $this->tasks()->recursiveCall();
		}
		return $this->attributes['tasks'];
	}
}
