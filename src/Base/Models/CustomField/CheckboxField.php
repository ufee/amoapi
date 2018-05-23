<?php
/**
 * amoCRM Entity custom field type - 3
 */
namespace Ufee\Amo\Base\Models\CustomField;

class CheckboxField extends EntityField
{
    /**
     * Set cf values
	 * @param integer $value
     */
    public function setValue($value)
    {
		$this->values = [
			['value' => (int)$value]
		];
		return $this;
    }
    
    /**
     * Set cf value to 1
     */
    public function enable()
    {
		return $this->setValue(1);
    }
    
    /**
     * Set cf value to 0
     */
    public function disable()
    {
		return $this->setValue(0);
	}
}
