<?php
/**
 * amoCRM Entity custom field type - 6
 */
namespace Ufee\Amo\Base\Models\CustomField;

class DateField extends EntityField
{
	/**
	 * Get formatted date
	 * @return string
	 */
	public function format($format)
	{
		if (!$date = $this->getValue()) {
			return null;
		}
		$date = new \DateTime($date, new \DateTimeZone($this->field->instance()->getAuth('timezone')));
		return $date->format($format);
	}
		
	/**
	 * Get date timestamp
	 * @return integer
	 */
	public function getTimestamp()
	{
		if (!$date = $this->getValue()) {
			return null;
		}
		$date = new \DateTime($date, new \DateTimeZone($this->field->instance()->getAuth('timezone')));
		return $date->getTimestamp();
	}
		
	/**
	 * Set date timestamp
	 * @param integer $stamp
	 */
	public function setTimestamp($stamp)
	{
		return $this->setValue(date('Y-m-d H:i:s', $stamp));
	}
		
	/**
	 * Set date
	 * @param string $date
	 */
	public function setDate($date)
	{
		return $this->setValue($date);
	}
}
