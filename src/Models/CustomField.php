<?php
/**
 * amoCRM Custom field
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Amoapi;
use Ufee\Amo\Oauthapi;

class CustomField extends \Ufee\Amo\Base\Models\Model
{
	protected 
		$system = [
			'client_id',
			'id',
			'name',
			'code',
			'field_type',
			'sort',
			'is_multiple',
			'is_system',
			'is_editable',
			'enums',
			'values_tree'
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
	
    /**
     * Get Amoapi instance
	 * @return ApiClient
     */
    public function instance()
    {
		$apiClass = is_numeric($this->client_id) ? Amoapi::class : Oauthapi::class;
		return $apiClass::getInstance($this->client_id);
	}
}
