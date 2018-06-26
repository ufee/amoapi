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
	 * @param string $field_name
	 * @return Collection
     */
	public function searchByCustomField($query, $field_name)
	{
		$query = trim($query);
		$results = $this->list->where('query', $query)->recursiveCall();	
		
		return $results->filter(function($model) use($query, $field_name) {
			return $query === trim($model->cf($field_name)->getValue());
		});	
	}
}
