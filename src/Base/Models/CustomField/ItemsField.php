<?php
/**
 * amoCRM Entity custom field type - 16
 */
namespace Ufee\Amo\Base\Models\CustomField;

class ItemsField extends EntityField
{
    /**
     * Get cf value
	 * @return mixed
     */
    public function getValue()
    {
		if (!isset($this->values[0]) || !isset($this->values[0][0])) {
			return null;
		}
		return $this->values[0][0];
	}

    /**
     * Get cf values
	 * @return array
     */
    public function getValues()
    {
        $values = [];
		foreach ($this->values as $setted) {
			foreach ($setted as $val) {
				$values[]= $val;
			}
        }
        return $values;
    }
}
