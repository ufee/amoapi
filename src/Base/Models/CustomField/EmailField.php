<?php
/**
 * amoCRM Entity custom field system - Email
 */
namespace Ufee\Amo\Base\Models\CustomField;

class EmailField extends EntityField
{
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
     * Set cf value
	 * @param string $value
     * @param string|integer $enum_key
     */
    public function setValue($value, $enum_key = 'Other')
    {
        $value = trim($value);
		if (is_numeric($enum_key) && isset($this->field->enums->{$enum_key})) {
			$enum = $enum_key;
			$enum_key = $this->field->enums->{$enum_key};
		} else {
			$enum_key = mb_strtoupper($enum_key);
			$enum = array_search($enum_key, (array)$this->field->enums);
		}
        if ($enum === false) {
            throw new \Exception('Invalid enum: "'.$enum_key.'" for cfield "'.$this->name.'" (enum not found)');
        }
        if (is_numeric($enum)) {
            $enum = intval($enum);
        }
        $new_values = [];
        foreach ($this->values as $setted) {
            if ($value == $setted->value) {
                continue;
            }
            $new_values[]= (object)[
                'value' => $setted->value, 'enum' => $setted->enum
            ];
        }
        $new_values[]= (object)[
            'value' => $value, 'enum' => $enum
        ];
       $this->values = $new_values;
       return $this;
    }
}
