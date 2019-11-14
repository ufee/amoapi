<?php
/**
 * amoCRM Entity custom field type - 3
 */
namespace Ufee\Amo\Base\Models\CustomField;

class CheckboxField extends EntityField
{
    /**
     * Get cf values
	 * @return array
     */
    public function getValues()
    {
        $values = [];
		foreach ($this->values as $setted) {
            $values[]= (int)$setted->value;
        }
        return $values;
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

    /**
     * Has checked box
     */
    public function hasChecked()
    {
		return (bool)$this->getValue();
	}
}
