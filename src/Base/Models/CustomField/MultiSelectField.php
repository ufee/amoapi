<?php
/**
 * amoCRM Entity custom field type - 5
 */
namespace Ufee\Amo\Base\Models\CustomField;

class MultiSelectField extends EntityField
{
    /**
     * Get cf values
	 * @return array
     */
    public function getValues()
    {
        $values = [];
		foreach ($this->values as $setted) {
            $values[]= htmlspecialchars_decode($setted->value, ENT_COMPAT);
        }
        return $values;
    }

    /**
     * Get cf enums
	 * @return array
     */
    public function getEnums()
    {
        $enums = [];
		foreach ($this->values as $setted) {
            $enums[]= $setted->enum;
        }
        return $enums;
    }

    /**
     * Reset cf values
     */
    public function reset()
    {
        $this->values = [];
        return $this;
    }

    /**
     * Set cf value
	 * @param string $value
     */
    public function setValue($value)
    {
        return $this->setValues([$value]);
    }

    /**
     * Set cf values
	 * @param array $values
     */
    public function setValues(Array $values)
    {
        $values = array_unique(array_merge($this->getValues(), $values));
        $enums = [];
        foreach($values as $value) {
            $value = htmlspecialchars($value, ENT_COMPAT);
            $enum = array_search($value, get_object_vars($this->field->enums));
            if ($enum === false) {
                throw new \Exception('Invalid value: "'.$value.'" for cfield "'.$this->name.'" (enum not found)');
            }
            $enums[]= $enum;
        }
        return $this->setEnums($enums);
    }

    /**
     * Set cf enum
	 * @param integer $enum
     */
    public function setEnum($enum)
    {
        return $this->setEnums([$enum]);
    }

    /**
     * Set cf enums
	 * @param array $enums
     */
    public function setEnums(Array $enums)
    {
        $enums = array_unique(array_merge($this->getEnums(), $enums));
        $new_values = [];
        foreach ($enums as $enum) {
            if (!array_key_exists($enum, get_object_vars($this->field->enums))) {
                throw new \Exception('Invalid enum: "'.$enum.'" for cfield "'.$this->name.'" (not found)');
            }
            $new_values[$enum]= (object)[
                'value' => $this->field->enums->{$enum},
                'enum' => $enum
            ];
        }
        $this->values = array_values($new_values);
        return $this;
    }

    /**
     * Get cf raw values
	 * @return array
     */
    public function getApiRawValues()
    {
		return $this->getEnums();
	}
}