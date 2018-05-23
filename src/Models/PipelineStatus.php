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
			'is_editable'
		],
		$hidden = [
			'pipeline'
		];
		
    /**
     * Constructor
	 * @param array $data
	 * @param Account $account
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
}
