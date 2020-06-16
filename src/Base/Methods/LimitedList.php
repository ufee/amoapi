<?php
/**
 * amoCRM API client GET list limited service method
 */
namespace Ufee\Amo\Base\Methods;

class LimitedList extends \Ufee\Amo\Base\Methods\Get
{
    /**
     * Recursive call api method
	 * @param array $arg
	 * @return Collection
     */
	public function recursiveCall($arg = [])
	{
		$collection_class = $this->service->entity_collection;
		$collection = new $collection_class([], $this->service);
		$limit_offset = 0;
		do {
			$result = $this->call(
				array_merge($arg, ['limit_rows' => $this->service->limit_rows, 'limit_offset' => $limit_offset])
			);
			$result->each(function(&$item) use(&$collection) {
				$collection->push($item);
			});
			$limit_offset+= $this->service->limit_rows;
		} while (
			$result->count() == $this->service->limit_rows && ($this->service->max_rows === 0 || ($collection->count()+$this->service->limit_rows) <= $this->service->max_rows)
		);
		$result = null;
		return $collection;
	}
}
