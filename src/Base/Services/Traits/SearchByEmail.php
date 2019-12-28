<?php
/**
 * amoCRM trait - search entitys by email
 */
namespace Ufee\Amo\Base\Services\Traits;

trait SearchByEmail
{
    /**
     * Get entitys by email
	 * @param string $email
	 * @return Collection
     */
	public function searchByEmail($email)
	{
		if (strlen($email) < 6 || !strpos($email, '@')) {
			throw new \Exception('Invalid search email value: '.$email);
		}
		$clearEmail = function($email) {
			return mb_strtoupper(trim($email));
		};
		$field_name = 'Email';
		$query = $clearEmail($email);
		$results = $this->list->where('query', $query)->recursiveCall();	
		
		return $results->filter(function($model) use($query, $field_name, $clearEmail) {
			foreach ($model->cf($field_name)->getValues() as $value) {
				if ($query === $clearEmail($value)) {
					return true;
				}
			}
			return false;
		});
	}
}
