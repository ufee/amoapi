<?php
/**
 * amoCRM trait - search entitys by name
 */
namespace Ufee\Amo\Base\Services\Traits;

trait SearchByName
{
    /**
     * Get entitys by name
	 * @param string $name
	 * @return Collection
     */
	public function searchByName($name)
	{
		function clearName($name) {
			return mb_strtoupper(trim($name));
		}
		$query = clearName($name);
		$results = $this->list->where('query', $query)->recursiveCall();	
		
		return $results->filter(function($model) use($query) {
			return $query === clearName($model->name);
		});
	}
}
