<?php
/**
 * amoCRM Entity custom field system - Im
 */
namespace Ufee\Amo\Base\Models\CustomField;

class ImField extends EntityField
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
     * @param string $enum_key
     */
    public function setValue($value, $enum_key = 'Other')
    {
        $enum_key = mb_strtoupper($enum_key);
        $enum = array_search($enum_key, (array)$this->field->enums);
         if ($enum === false) {
            throw new \Exception('Invalid enum: "'.$enum_key.'" for cfield "'.$this->name.'" (enum not found)');
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