<?php
/**
 * amoCRM Pipeline Pipeline statuses Collection class
 */
namespace Ufee\Amo\Collections;
use Ufee\Amo\Models\Pipeline,
  Ufee\Amo\Models\PipelineStatus;

class PipelineStatusesCollection extends CollectionWrapper
{
    /**
     * Constructor
     * @param array $elements
     * @param Pipeline $pipeline
     */
    public function __construct(Array $elements = [], Pipeline &$pipeline)
    {
		$this->collection = new \Ufee\Amo\Base\Collections\Collection($elements);
		$this->collection->each(function(&$item) use ($pipeline) {
			$item = new PipelineStatus($item, $pipeline);
		});
	}
	
	/**
     * Get status success (142)
     * @return PipelineStatus
     */
    public function success()
    {
		return $this->collection->find('id', 142)->first();
	}
	
	/**
     * Get status loss (143)
     * @return PipelineStatus
     */
    public function loss()
    {
		return $this->collection->find('id', 143)->first();
	}
}
