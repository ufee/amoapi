<?php
/**
 * amoCRM trait - search entitys by phone
 */
namespace Ufee\Amo\Base\Services\Traits;

trait SearchByPhone
{
    /**
     * Get entitys by phone
	 * @param string $phone
	 * @param string $format
	 * @param integer $max_rows
	 * @return Collection
     */
	public function searchByPhone($phone, $format = self::PHONE_RU_MOB, $max_rows = 100)
	{
		$method = 'searchBy_'.$format;
		if (!method_exists($this, $method)) {
			throw new \Exception('Invalid search format: '.(string)$format);
		}
		return call_user_func(
			[$this, $method], $phone, $max_rows
		);
	}
	
    /**
     * Get by phone Ru Mobile
	 * @param string $phone
	 * @param integer $max_rows
	 * @return Collection
     */
	protected function searchBy_ru_mob($phone, $max_rows)
	{
		$clearPhone = function($phone) {
			return substr(preg_replace('#[^0-9]+#Uis', '', $phone), -10);
		};
		$field_name = $this->instance->getAuth('lang') == 'ru' ? 'Телефон' : 'Phone';
		$query = $clearPhone($phone);
		if (strlen($query) < 6) {
			throw new \Exception('Invalid search phone value: '.$phone);
		}
		$prev_max_rows = $this->max_rows;
		$set_max_rows = $max_rows >= $this->limit_rows ? $max_rows+$this->limit_rows : $max_rows;

		$results = $this->maxRows($set_max_rows)->list->where('query', $query)->recursiveCall();	
		$results = $results->filter(function($model) use($query, $field_name, $clearPhone) {
			foreach ($model->cf($field_name)->getValues() as $value) {
				if ($query === $clearPhone($value)) {
					return true;
				}
			}
			return false;
		});
		if ($max_rows > 0) {
			$results->slice(0, $max_rows);
		}
		$this->maxRows($prev_max_rows);
		return $results;
	}
}
