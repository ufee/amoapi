<?php
/**
 * amoCRM Custom field
 */
namespace Ufee\Amo\Models;

class CustomField extends \Ufee\Amo\Base\Models\Model
{
	protected 
		$system = [
			'id',
			'name',
			'field_type',
			'sort',
			'is_multiple',
			'is_system',
			'is_editable',
			'enums'
		];
	
    /**
     * Is multiple field
	 * @return bool
     */
    public function isMultiple()
    {
		return (bool)$this->is_multiple;
	}

    /**
     * Is system field
	 * @return bool
     */
    public function isSystem()
    {
		return (bool)$this->is_system;
	}

    /**
     * Is editable field
	 * @return bool
     */
    public function isEditable()
    {
		return (bool)$this->is_editable;
	}
}
