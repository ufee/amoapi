<?php
/**
 * amoCRM Entity custom field type - 13
 */
namespace Ufee\Amo\Base\Models\CustomField;

class SmartAddressField extends EntityField
{
    /**
     * Set index
	 * @param string $value
     */
    public function setCountry($value)
    {
        return $this->setBySubtype(6, $value);
    }

    /**
     * Set region
	 * @param string $value
     */
    public function setRegion($value)
    {
        return $this->setBySubtype(4, $value);
    }
    
    /**
     * Set city
	 * @param string $value
     */
    public function setCity($value)
    {
        return $this->setBySubtype(3, $value);
    }

    /**
     * Set index
	 * @param string $value
     */
    public function setIndex($value)
    {
        return $this->setBySubtype(5, $value);
    }

    /**
     * Set address
	 * @param string $value
     */
    public function setAddress($value)
    {
        return $this->setBySubtype(1, $value);
    }

     /**
     * Set address living
	 * @param string $value
     */
    public function setLive($value)
    {
        return $this->setBySubtype(2, $value);
    }

     /**
     * Set by subtype
     * @param integer $subtype
	 * @param string $value
     */
    public function setBySubtype($subtype, $value)
    {
        $values = [];
        foreach ($this->values as $setted) {
            $values[$setted->subtype] = $setted;
        }
        $values[$subtype] = (object)[
            'value' => $value, 'subtype' => $subtype
        ];
        $this->values = $values;
        return $this;
    }
}
