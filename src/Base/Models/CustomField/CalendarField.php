<?php
/**
 * amoCRM Entity custom field type - 19
 */
namespace Ufee\Amo\Base\Models\CustomField;
use Ufee\Amo\Amoapi;

class CalendarField extends EntityField
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
		$date = new \DateTime($date, new \DateTimeZone(Amoapi::getInstance($this->account_id)->getAuth('timezone')));
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
		$date = new \DateTime($date, new \DateTimeZone(Amoapi::getInstance($this->account_id)->getAuth('timezone')));
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
