<?php
/**
 * amoCRM Pipeline status model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Collections\PipelineStatusesCollection;

class PipelineStatus extends \Ufee\Amo\Base\Models\Model
{
	protected
		$system = [
			'id',
			'name',
			'color',
			'sort',
			'is_editable',
			'next',
			'prev',
			'nexts',
			'prevs'
		],
		$hidden = [
			'pipeline'
		];
		
    /**
     * Constructor
	 * @param array $data
	 * @param Pipeline $pipeline
     */
    public function __construct($data, \Ufee\Amo\Models\Pipeline &$pipeline)
    {
		if (!is_array($data) && !is_object($data)) {
			throw new \Exception(static::getBasename().' model data must be array or object');
		}
        foreach ($this->system as $field_key) {
			$this->attributes[$field_key] = null;
		}
        foreach ($this->hidden as $field_key) {
			$this->attributes[$field_key] = null;
		}
        foreach ($this->writable as $field_key) {
			$this->attributes[$field_key] = null;
		}
        foreach ($data as $field_key=>$val) {
			if (array_key_exists($field_key, $this->attributes)) {
				$this->attributes[$field_key] = $val;
			}
		}
		$this->attributes['pipeline'] = $pipeline;
		$this->_boot($data);
	}

	/**
     * Has this status next from arg status
	 * @param PipelineStatus|integer $status_id
     * @return bool
     */
    public function hasNextFrom($status_id)
    {
		if (!$status = $this->detectStatus($status_id)) {
			throw new \Exception('Invalid status given: PipelineStatus not found');
		}
		return ($this->sort - $status->sort) === 10;
	}

	/**
     * Has this status prev from arg status
	 * @param PipelineStatus|integer $status_id
     * @return bool
     */
    public function hasPrevFrom($status_id)
    {
		if (!$status = $this->detectStatus($status_id)) {
			throw new \Exception('Invalid status given: PipelineStatus not found');
		}
		return ($status->sort - $this->sort) === 10;
	}
	
	/**
     * Has this status before arg status
	 * @param PipelineStatus|integer $status_id
     * @return bool
     */
    public function hasBeforeFrom($status_id)
    {
		if (!$status = $this->detectStatus($status_id)) {
			throw new \Exception('Invalid status given: PipelineStatus not found');
		}
		return $this->sort < $status->sort;
	}

	/**
     * Has this status after arg status
	 * @param PipelineStatus|integer $status_id
     * @return bool
     */
    public function hasAfterFrom($status_id)
    {
		if (!$status = $this->detectStatus($status_id)) {
			throw new \Exception('Invalid status given: PipelineStatus not found');
		}
		return $this->sort > $status->sort;
	}

	/**
     * Has status first
     * @return bool
     */
    public function hasFirst()
    {
		return $this->id === $this->pipeline->statuses->first()->id;
	}
	
	/**
     * Has status open
     * @return bool
     */
    public function hasOpen()
    {
		return !$this->hasClosed();
	}
	
	/**
     * Has status closed
     * @return bool
     */
    public function hasClosed()
    {
		return in_array($this->id, [142, 143]);
	}

	/**
     * Has status success
     * @return bool
     */
    public function hasSuccess()
    {
		return $this->id == 142;
	}

	/**
     * Has status missed
     * @return bool
     */
    public function hasMissed()
    {
		return $this->id == 143;
	}

	/**
     * Has status last
     * @return bool
     */
    public function hasLast()
    {
		return $this->hasClosed();
	}

	/**
     * Detect status from arg
	 * @param mixed $status
     * @return PipelineStatus|null
     */
    public function detectStatus($status)
    {
		if ($status instanceof PipelineStatus) {
			return $status;
		}
		if (is_numeric($status)) {
			return $this->pipeline->statuses->byId($status);
		}
		if (is_object($status) && isset($status->id)) {
			return $this->pipeline->statuses->byId($status->id);
		}
		if (is_array($status) && isset($status['id'])) {
			return $this->pipeline->statuses->byId($status['id']);
		}
		return null;
	}

	/**
	 * Get prevs of this status
	 * @return Collection
	 */
	protected function prevs_access()
	{
		if (is_null($this->attributes['prevs'])) {
			$current = $this;
			$this->attributes['prevs'] = $this->pipeline->statuses->find(function($status) use($current) {
				return !$status->hasSuccess() && !$status->hasMissed() && $status->hasBeforeFrom($current);
			});	
		}
		return $this->attributes['prevs'];	
	}

	/**
	 * Get nexts of this status
	 * @return Collection
	 */
	protected function nexts_access()
	{
		if (is_null($this->attributes['nexts'])) {
			$current = $this;
			$this->attributes['nexts'] = $this->pipeline->statuses->find(function($status) use($current) {
				return !$status->hasMissed() && $status->hasAfterFrom($current);
			});
		}
		return $this->attributes['nexts'];	
	}

	/**
	 * Get prev of this status
	 * @return PipelineStatus|null
	 */
	protected function prev_access()
	{
		return $this->prevs->last();	
	}

	/**
	 * Get next of this status
	 * @return PipelineStatus|null
	 */
	protected function next_access()
	{
		return $this->nexts->first();	
	}
}
