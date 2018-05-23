<?php
/**
 * amoCRM Pipeline model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Collections\PipelineStatusesCollection;

class Pipeline extends \Ufee\Amo\Base\Models\Model
{
	protected
		$system = [
			'id',
			'name',
			'sort',
			'is_main',
			'statuses'
		],
		$hidden = [
			'account_id'
		];
	
    /**
     * Constructor
	 * @param array $data
	 * @param Account $account
     */
    public function __construct($data, \Ufee\Amo\Models\Account &$account)
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
		$this->attributes['account_id'] = $account->id;
		$this->attributes['statuses'] = new PipelineStatusesCollection(
			(array)$this->attributes['statuses'], $this
		);
    }
	
	/**
     * Has main pipeline
     * @return bool
     */
    public function hasMain()
    {
		return (bool)$this->is_main;
	}
}
