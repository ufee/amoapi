<?php
/**
 * amoCRM Task model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Base\Models\Traits;
use Ufee\Amo\Amoapi;

class Task extends \Ufee\Amo\Base\Models\ApiModel
{
	use Traits\LinkedParents, Traits\EntityDetector;

	protected static 
		$_type = 'task';
	protected
		$hidden = [
			'query_hash',
			'service',
			'linkedLead',
			'linkedContact',
			'linkedCompany',
			'createdUser',
			'responsibleUser',
			'taskType',
			'note'
		],
		$writable = [
			'element_id',
			'element_type',
			'complete_till_at',
			'duration',
			'task_type',
			'text',
			'responsible_user_id',
			'created_at',
			'updated_at',
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
     * Get task expired status
     * @return bool
     */
    public function hasExpired()
    {
		$date = new \DateTime('now', new \DateTimeZone(Amoapi::getInstance($this->account_id)->getAuth('timezone')));
		return !$this->is_completed && $date->format('Y-m-d H:i') > $this->endDate('Y-m-d H:i');
	}

    /**
     * Get task start date
     * @return string
     */
    public function startDate($format = 'Y-m-d H:i:s')
    {
		$date = new \DateTime();
		$date->setTimestamp($this->complete_till_at);
		if (date('H:i', $this->complete_till_at) == '23:59') {
			$date->setTime(0,0,0);
		}
		$date->setTimezone(new \DateTimeZone(Amoapi::getInstance($this->account_id)->getAuth('timezone')));
		return $date->format($format);
	}

    /**
     * Get task end date
     * @return string
     */
    public function endDate($format = 'Y-m-d H:i:s')
    {
		$date = new \DateTime();
		$date->setTimestamp($this->complete_till_at+$this->duration);
		$date->setTimezone(new \DateTimeZone(Amoapi::getInstance($this->account_id)->getAuth('timezone')));
		return $date->format($format);
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
