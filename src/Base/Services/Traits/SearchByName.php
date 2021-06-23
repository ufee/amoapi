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
	 * @param integer $max_rows
	 * @return Collection
     */
	public function searchByName($name, $max_rows = 100)
	{
		$clearName = function($name) {
			return mb_strtoupper(trim($name));
		};
		$query = $clearName($name);
		$prev_max_rows = $this->max_rows;
		$set_max_rows = $max_rows >= $this->limit_rows ? $max_rows+$this->limit_rows : $max_rows;

		$results = $this->maxRows($set_max_rows)->list->where('query', $query)->recursiveCall();	
		$results = $results->filter(function($model) use($query, $clearName) {
			return $query === $clearName($model->name);
		});
		if ($max_rows > 0) {
			$results->slice(0, $max_rows);
		}
		$this->maxRows($prev_max_rows);
		return $results;
	}
}
