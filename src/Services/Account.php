<?php
/**
 * amoCRM API client Accounts service
 */
namespace Ufee\Amo\Services;

class Account extends \Ufee\Amo\Base\Services\Cached
{
	protected static $_cache_time = 600;
	protected $methods = [
					'current'
			];
	protected $_current_data;
	
    /**
     * Service on load
	 * @return void
     */
	protected function _boot()
	{
		$this->api_args = [
			'with' => 'users,groups,pipelines,custom_fields,note_types,task_types',
			'lang' => $this->instance->getAuth('lang')
		];
	}
	
    /**
     * Get with arguments
	 * @param mixed
     */
	public function with()
	{
		$values = func_get_args();
		if (count($values) == 1) {
			$values = $values[0];
		}
		return $this->current->call([
			'with' => join(',', $values)
		]);
	}
	
    /**
     * Get full
	 * @return Account
     */
	public function account()
	{
		if (is_null($this->_current_data)) {
			$this->_current_data = $this->current->call();
		}
		return $this->_current_data;
	}
}
