<?php
/**
 * amoCRM Entity custom field type - 24
 */
namespace Ufee\Amo\Base\Models\CustomField;

class ChainedList extends EntityField
{
    /**
     * Get cf value
	 * @return mixed
     */
    public function getValue()
    {
		return $this->getValues();
	}
	
    /**
     * Get cf values
	 * @return mixed
     */
    public function getValues()
    {
		if (!isset($this->values[0])) {
			return null;
		}
		return $this->values;
	}
	
    /**
     * Set value
	 * @param array $values
     */
    public function setValue($values)
    {
		return $this->setValues($values);
	}
	
    /**
     * Set values
	 * @param array $values
     */
    public function setValues($values)
    {
		$this->values = $values;
	}
}