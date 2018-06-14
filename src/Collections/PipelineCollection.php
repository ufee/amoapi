<?php
/**
 * amoCRM Pipeline Pipeline Collection class
 */
namespace Ufee\Amo\Collections;
use Ufee\Amo\Models\Pipeline;

class PipelineCollection extends CollectionWrapper
{
	/**
	 * Constructor
	 * @param array $elements
	 * @param Account $account
	 */
	public function __construct(Array $elements = [], \Ufee\Amo\Models\Account &$account)
	{
		$this->collection = new \Ufee\Amo\Base\Collections\Collection($elements);
		$this->collection->each(function(&$item) use($account) {
			$item = new Pipeline($item, $account);
		});
		$this->attributes['account'] = $account;
	}
	
	/**
	 * Get pipeline from id
	 * @param $id - pipeline id
	 * @return Pipeline
	 */
	public function byId($id)
	{
		return $this->collection->find('id', $id)->first();
	}

	/**
	 * Get main pipeline
	 * @return Pipeline
	 */
	public function main()
	{
		return $this->collection->find('is_main', 1)->first();
	}
}
