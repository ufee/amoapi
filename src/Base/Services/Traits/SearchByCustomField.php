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
		
		$collClass = get_class($results);
		$service = $results->service();
		$searched = new $collClass([], $service);
		$results = $results->all();
		
		foreach ($results as &$model) {
			$cf = null;
			if ($field_type == 'integer') {
				$cf = $model->cf()->byId($field);
			} elseif ($field_type == 'string') {
				$cf = $model->cf($field);
			}
			if (!$cf) {
				throw new \Exception('Custom Field not found by '.($field_type == 'integer' ? 'id' : 'name').': '.$field);
			}
			$value = trim((string)$cf->getValue());
			if ($query === $value) {
				$searched->push($model);
			}
			$model = null;
			unset($model);
			usleep(50);
		}
		$results = null;
		unset($results);
		
		if ($max_rows > 0) {
			$searched->slice(0, $max_rows);
		}
		$this->maxRows($prev_max_rows);
		return $searched;
	}
}
