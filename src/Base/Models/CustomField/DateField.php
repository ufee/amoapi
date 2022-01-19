<?php
/**
 * amoCRM Entity custom field type - 6
 */
namespace Ufee\Amo\Base\Models\CustomField;

class DateField extends EntityField
{
	/**
	 * Get DateTime from cf value
	 * @param string|null $timezone - Europe/Moscow
	 * @return \DateTime
	 */
	public function getDateTime($timezone = null)
	{
		if (!$date = $this->getValue()) {
			return null;
		}
		if (is_null($timezone)) {
			$timezone = $this->field->instance()->getAuth('timezone');
		}
		return new \DateTime($date, new \DateTimeZone($timezone));
	}
	
	/**
	 * Get formatted date
	 * @param string $format - Y-m-d
	 * @param string|null $timezone - Europe/Moscow
	 * @return string
	 */
	public function format($format, $timezone = null)
	{
		if (!$date = $this->getValue()) {
			return null; 
		}
		return $this->getDateTime($timezone)->format($format);
	}
	
	/**
	 * Get date timestamp
	 * @param string|null $timezone - Europe/Moscow ...
	 * @return integer
	 */
	public function getTimestamp($timezone = null)
	{
		if (!$date = $this->getValue()) {
			return null; 
		}
		return $this->getDateTime($timezone)->getTimestamp();
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
