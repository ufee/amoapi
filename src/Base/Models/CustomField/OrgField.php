<?php
/**
 * amoCRM Entity custom field system - Organization
 */
namespace Ufee\Amo\Base\Models\CustomField;

class OrgField extends EntityField
{
    const ORG_STRUCTURE = [
        'name' => '',
        'entity_type' => '',
        'vat_id' => '',
        'tax_registration_reason_code' => '',
        'address' => '',
        'kpp' => '',
		'bank_code' => '',
        'external_uid' => '',
        'line1' => '',
        'line2' => '',
        'city' => '',
        'state' => '',
        'zip' => '',
        'country' => ''
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
		return $this->values[0]->value->name;
	}

    /**
     * Get cf values
	 * @return array
     */
    public function getValues()
    {
		if (!isset($this->values[0])) {
			return null;
		}
		return (array)$this->values[0]->value;
    }

    /**
     * Add organization
	 * @param array $value
     */
    public function addValue(array $value)
    {
        $new_values = $this->values;
        $org = static::ORG_STRUCTURE;
        foreach ($org as $key=>$val) {
            if (isset($value[$key])) {
                $org[$key] = $value[$key];
            }
        }
        $new_values[]= (object)[
            'value' => $org
        ];
        $this->values = $new_values;
        return $this;
    }

    /**
     * Set organization
	 * @param array $value
     */
    public function setValue($value)
    {
        $org = static::ORG_STRUCTURE;
        foreach ($org as $key=>$val) {
            if (isset($value[$key])) {
                $org[$key] = $value[$key];
            }
        }
        $this->values = [
            (object)[
                'value' => (object)$org
            ]
        ];
    }

    /**
     * Remove organization
	 * @param string $key
     * @param string $value
     */
    public function removeBy($key, $value)
    {
        $new_values = [];
		foreach ($this->values as $i=>$setted) {
            $org = (array)$setted->value;
            if (!isset($org[$key]) || $org[$key] != $value) {
                $new_values[]= $setted;
            }
        }
        $this->values = $new_values;
        return $this;
    }
}
