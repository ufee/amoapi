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
	 * @param integer $max_rows
	 * @return Collection
     */
	public function searchByCustomField($query, $field, $max_rows = 100)
	{
		$query = trim($query);
		$field_type = gettype($field);
		$prev_max_rows = $this->max_rows;
		$set_max_rows = $max_rows >= $this->limit_rows ? $max_rows+$this->limit_rows : $max_rows;

		$results = $this->maxRows($set_max_rows)->list->where('query', $query)->recursiveCall();
		$results = $results->filter(function($model) use($query, $field, $field_type) {
			$cf = null;
			if ($field_type == 'integer') {
				$cf = $model->cf()->byId($field);
			} elseif ($field_type == 'string') {
				$cf = $model->cf($field);
			}
			if (!$cf) {
				throw new \Exception('Custom Field not found by '.($field_type == 'integer' ? 'id' : 'name').': '.$field);
			}
			return $query === trim($cf->getValue());
		});	
		if ($max_rows > 0) {
			$results->slice(0, $max_rows);
		}
		$this->maxRows($prev_max_rows);
		return $results;
	}
}
