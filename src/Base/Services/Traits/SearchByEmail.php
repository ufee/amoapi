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
		function clear($email) {
			return mb_strtoupper(trim($email));
		}
		$field_name = 'Email';
		$query = clear($email);
		$results = $this->list->where('query', $query)->recursiveCall();	
		
		return $results->filter(function($model) use($query, $field_name) {
			foreach ($model->cf($field_name)->getValues() as $value) {
				if ($query === clear($value)) {
					return true;
				}
			}
			return false;
		});
	}
}
