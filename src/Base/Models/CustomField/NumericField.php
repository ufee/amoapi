<?php
/**
 * amoCRM Entity custom field type - 2
 */
namespace Ufee\Amo\Base\Models\CustomField;

class NumericField extends EntityField
{
    /**
     * Get cf value
	 * @return mixed
     */
    public function getValue()
    {
		if (!isset($this->values[0])) {
			return null;
		}
		return $this->values[0]->value;
	}

    /**
     * Get cf values
	 * @return array
     */
    public function getValues()
    {
        $values = [];
		foreach ($this->values as $setted) {
            $values[]= $setted->value;
        }
        return $values;
    }
}
