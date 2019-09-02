<?php
/**
 * amoCRM Entity custom field
 */
namespace Ufee\Amo\Base\Models\CustomField;

class EntityField extends \Ufee\Amo\Base\Models\Model
{
	protected 
		$system = [
			'id',
			'account_id',
			'name'
		],
		$hidden = [
			'field'
		],
		$writable = [
			'values'
		];

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

    /**
     * Set cf values
	 * @param mixed $value value
     */
    public function setValue($value)
    {
		$this->values = [
			(object)['value' => $value]
		];
		return $this;
	}

    /**
     * Reset cf value
     */
    public function reset()
    {
		$this->values = [];
		return $this;
	}

    /**
     * Get cf raw
	 * @return array
     */
    public function getRaw()
    {
		return [
			'id' => $this->id,
			'name' => $this->name,
			'values' => $this->values,
			'is_system' => $this->field->is_system
		];
	}
	
    /**
     * Get cf raw
	 * @return array
     */
    public function getApiRaw()
    {
		return [
			'id' => $this->id,
			'values' => $this->getApiRawValues()
		];
	}

    /**
     * Get cf raw values
	 * @return array
     */
    public function getApiRawValues()
    {
		return $this->values;
	}
}
