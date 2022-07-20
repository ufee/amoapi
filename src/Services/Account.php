<?php
/**
 * amoCRM API client Accounts service
 */
namespace Ufee\Amo\Services;

class Account extends \Ufee\Amo\Base\Services\Cached
{
	protected static $_cache_time = 600;
	protected static $_current_data = [];
	
	protected $id = null;
	protected $domain = null;
	protected $methods = [
		'current'
	];

    /**
     * Service on load
	 * @return void
     */
	protected function _boot()
	{
		$this->id = $this->instance->getAuth('id');
		$this->domain = $this->instance->getAuth('domain');
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
		$key = $this->domain.$this->id;
		if (!array_key_exists($key, static::$_current_data)) {
			static::$_current_data[$key] = $this->current->call();
		}
		return static::$_current_data[$key];
	}
}
