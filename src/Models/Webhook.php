<?php
/**
 * amoCRM Webhook model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Base\Models\Traits;

class Webhook extends \Ufee\Amo\Base\Models\ApiModel
{
	protected
		$hidden = [
			'query_hash',
			'service'
		],
		$writable = [
			'id',
			'url',
			'disabled',
			'events'
		];
	
    /**
     * Model on load
	 * @param array $data
	 * @return void
     */
    protected function _boot($data = [])
    {
		parent::_boot($data);
	}
	
    /**
     * Convert Model to array
     * @return array
     */
    public function toArray()
    {
		$fields = parent::toArray();
		return $fields;
    }
}
