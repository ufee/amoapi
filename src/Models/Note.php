<?php
/**
 * amoCRM Note model
 */
namespace Ufee\Amo\Models;

class Note extends \Ufee\Amo\Base\Models\ApiModel
{
	protected
		$hidden = [
			'query_hash',
			'service',
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
     * Convert Model to array
     * @return array
     */
    public function toArray()
    {
		$fields = parent::toArray();
		return $fields;
    }
}
