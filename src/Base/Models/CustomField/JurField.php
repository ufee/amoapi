<?php
/**
 * amoCRM Entity custom field type - 15
 */
namespace Ufee\Amo\Base\Models\CustomField;

class JurField extends EntityField
{
    /**
     * Get cf value
	 * @return mixed
     */
    public function getValue()
    {
		if (!isset($this->values[0])) {
			return (object)[
                'name' => '',
                'entity_type' => '',
                'vat_id' => '',
                'tax_registration_reason_code' => '',
                'address' => '',
                'kpp' => '',
                'external_uid' => ''
            ];
        }
		return $this->values[0]->value;
	}

    /**
     * Set client name
	 * @param string $value
     */
    public function setName($value)
    {
        $setted = $this->getValue();
        $setted->name = $value;
        return $this->setValue($setted);
    }

    /**
     * Set client type
	 * @param integer $value
     */
    public function setType($value)
    {
        $setted = $this->getValue();
        $setted->entity_type = $value;
        return $this->setValue($setted);
    }

    /**
     * Set cf value
	 * @param string $value
     */
    public function setAddress($value)
    {
        $setted = $this->getValue();
        $setted->address = $value;
        return $this->setValue($setted);
    }

    /**
     * Set client INN
	 * @param integer $value
     */
    public function setInn($value)
    {
        $setted = $this->getValue();
        $setted->vat_id = $value;
        return $this->setValue($setted);
    }

    /**
     * Set client KPP
	 * @param integer $value
     */
    public function setKpp($value)
    {
        $setted = $this->getValue();
        $setted->kpp = $value;
        return $this->setValue($setted);
    }

    /**
     * Set external uid
	 * @param integer $value
     */
    public function externalUid($value)
    {
        $setted = $this->getValue();
        $setted->external_uid = $value;
        return $this->setValue($setted);
    }

    /**
     * Set tax registration reason code
	 * @param integer $value
     */
    public function taxRegistrationReasonCode($value)
    {
        $setted = $this->getValue();
        $setted->tax_registration_reason_code = $value;
        return $this->setValue($setted);
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
     * Get cf raw values
	 * @return array
     */
    public function getApiRawValues()
    {
		return [
            (object)['value' => $this->getValue()]
        ];
	}
}