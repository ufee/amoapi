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
		function clearEmail($email) {
			return mb_strtoupper(trim($email));
		}
		$field_name = 'Email';
		$query = clearEmail($email);
		$results = $this->list->where('query', $query)->recursiveCall();	
		
		return $results->filter(function($model) use($query, $field_name) {
			foreach ($model->cf($field_name)->getValues() as $value) {
				if ($query === clearEmail($value)) {
					return true;
				}
			}
			return false;
		});
	}
}
