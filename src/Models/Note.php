<?php
/**
 * amoCRM Note model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Base\Models\Interfaces\EntityDetector;
use Ufee\Amo\Base\Models\Interfaces\LinkedParents;
use Ufee\Amo\Base\Models\Traits;

class Note extends \Ufee\Amo\Base\Models\ApiModel implements LinkedParents,EntityDetector
{
	use Traits\LinkedParents, Traits\EntityDetector;

	protected
		$hidden = [
			'query_hash',
			'service',
			'linkedLead',
			'linkedContact',
			'linkedCompany',
			'createdUser',
			'responsibleUser',
			'noteType'
		],
		$writable = [
			'element_id',
			'element_type',
			'is_editable',
			'note_type',
			'text',
			'responsible_user_id',
			'updated_at',
			'created_at',
			'created_by',
			'attachment',
			'params'
		];
	
    /**
     * Model on load
	 * @param array $data
	 * @return void
     */
    protected function _boot($data = [])
    {
		parent::_boot($data);
	}

    /**
     * Set Note pinned
	 * @param bool $state
     * @return bool
     */
    public function setPinned($state = true)
    {
		try {
			$this->service->instance->ajax()->setNotePinned($this->id, $state);
			return true;
		} catch (\Exception $e) {
			return false;
		}
	}

    /**
     * Get attachment contents
	 * return string
     */
	public function getAttachment()
	{
		return $this->service->instance->ajax()->getAttachment($this->attachment);
	}
	
    /**
     * Convert Model to array
     * @return array
     */
    public function toArray()
    {
		$fields = parent::toArray();
		return $fields;
    }
}
