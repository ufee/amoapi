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
	 * @param integer $limit
	 * @return Collection
     */
	public function searchByEmail($email, $max_rows = 100)
	{
		if (strlen($email) < 6 || !strpos($email, '@')) {
			throw new \Exception('Invalid search email value: '.$email);
		}
		$clearEmail = function($email) {
			return mb_strtoupper(trim($email));
		};
		$field_name = 'Email';
		$query = $clearEmail($email);
		$prev_max_rows = $this->max_rows;
		$set_max_rows = $max_rows >= $this->limit_rows ? $max_rows+$this->limit_rows : $max_rows;
		$results = $this->maxRows($set_max_rows)->list->where('query', $query)->recursiveCall();

		$collClass = get_class($results);
		$service = $results->service();
		$searched = new $collClass([], $service);
		$results = $results->all();
		
		foreach ($results as &$model) {
			foreach ($model->cf($field_name)->getValues() as $value) {
				if ($query === $clearEmail($value)) {
					$searched->push($model);
				}
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
