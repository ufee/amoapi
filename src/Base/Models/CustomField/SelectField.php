<?php
/**
 * amoCRM Entity custom field type - 4
 */
namespace Ufee\Amo\Base\Models\CustomField;

class SelectField extends EntityField
{
    /**
     * Get cf enum
	 * @return integer
     */
    public function getEnum()
    {
		if (!isset($this->values[0])) {
			return null;
        }
		return $this->values[0]->enum;
    }

    /**
     * Set cf value
	 * @param string $value
     */
    public function setValue($value)
    {
        $enum = array_search($value, get_object_vars($this->field->enums));
        if ($enum === false) {
            throw new \Exception('Invalid value: "'.$value.'" for cfield "'.$this->name.'" (enum not found)');
        }
		$this->values = [
			(object)['value' => $value, 'enum' => $enum]
        ];
        return $this;
    }

    /**
     * Set cf enum
	 * @param integer $enum
     */
    public function setEnum($enum)
    {
        if (!array_key_exists($enum, get_object_vars($this->field->enums))) {
            throw new \Exception('Invalid enum: "'.$enum.'" for cfield "'.$this->name.'" (not found)');
        }
		$this->values = [
			(object)['value' => '', 'enum' => $enum]
        ];
        return $this;
    }

    /**
     * Get cf raw values
	 * @return array
     */
    public function getApiRawValues()
    {
		return [
            ['value' => $this->getEnum()]
        ];
	}
}
