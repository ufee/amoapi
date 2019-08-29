<?php
/**
 * amoCRM Task model
 */
namespace Ufee\Amo\Models;

class Task extends \Ufee\Amo\Base\Models\ApiModel
{
	protected static
		$_type = 'task';
	protected
		$hidden = [
			'query_hash',
			'service',
			'createdUser',
			'responsibleUser',
			'taskType',
			'note'
		],
		$writable = [
			'element_id',
			'element_type',
			'complete_till_at',
			'task_type',
			'text',
			'responsible_user_id',
			'updated_at',
            'created_at',
			'is_completed',
			'created_by',
			'result'
		];

    /**
     * Model on load
	 * @param array $data
	 * @return void
     */
    protected function _boot($data = [])
    {
		parent::_boot($data);
		if (isset($data->result)) {
			$this->attributes['result']->entity = null;
		}
		unset(
			$this->attributes['result']->_links
		);
	}

    /**
     * Has linked result
     * @return bool
     */
    public function hasResult()
    {
		return isset($this->attributes['result']->id);
	}

    /**
     * Get linked result
	 * @param bool $force
     * @return Note|null
     */
    public function getResult($force = false)
    {
		if (!isset($this->attributes['result']->id)) {
			return null;
		}
		if (is_null($this->attributes['result']->entity) || $force) {
			$this->attributes['result']->entity = $this->service->instance->notes()->where('type', 'task')->where('element_id', $this->attributes['result']->id)->call()->first();
		}
		return $this->attributes['result']->entity;
	}

    /**
     * Convert Model to array
     * @return array
     */
    public function toArray()
    {
		$fields = parent::toArray();
		$fields['result_text'] =  '';
		if (isset($this->attributes['result']->id)) {
			$fields['result_text'] =  $this->attributes['result']->text;
		}
		unset($fields['result']);
		return $fields;
    }
}
