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
	 * @return Collection
     */
	public function searchByPhone($phone, $format = self::PHONE_RU_MOB)
	{
		$method = 'searchBy_'.$format;
		if (!method_exists($this, $method)) {
			throw new \Exception('Invalid search format: '.(string)$format);
		}
		return call_user_func(
			[$this, $method], $phone
		);
	}
	
    /**
     * Get by phone Ru Mobile
	 * @param string $phone
	 * @return Collection
     */
	protected function searchBy_ru_mob($phone)
	{
		$clearPhone = function($phone) {
			return substr(preg_replace('#[^0-9]+#Uis', '', $phone), -10);
		};
		$field_name = $this->instance->getAuth('lang') == 'ru' ? 'Телефон' : 'Phone';
		$query = $clearPhone($phone);
		if (strlen($query) < 6) {
			throw new \Exception('Invalid search phone value: '.$phone);
		}
		$results = $this->list->where('query', $query)->recursiveCall();	
		
		return $results->filter(function($model) use($query, $field_name, $clearPhone) {
			foreach ($model->cf($field_name)->getValues() as $value) {
				if ($query === $clearPhone($value)) {
					return true;
				}
			}
			return false;
		});
	}
}
