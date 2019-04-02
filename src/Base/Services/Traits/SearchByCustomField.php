<?php
/**
 * amoCRM Base trait - search entitys by cf
 */
namespace Ufee\Amo\Base\Services\Traits;

trait SearchByCustomField
{
    /**
     * Get entitys by cf
	 * @param string $query
	 * @param integer|string $field
	 * @return Collection
     */
	public function searchByCustomField($query, $field)
	{
		$query = trim($query);
		$results = $this->list->where('query', $query)->recursiveCall();	
		
		return $results->filter(function($model) use($query, $field) {
			$cf = null;
			if (gettype($field) == 'integer') {
				$cf = $model->cf()->byId($field);
			} elseif (gettype($field) == 'string') {
				$cf = $model->cf($field);
			}
			if (!$cf) {
				throw new \Exception('Custom Field not found by '.(gettype($field) == 'integer' ? 'id' : 'name').': '.$field);
			}
			return $query === trim($cf->getValue());
		});	
	}
}
