<?php
/**
 * amoCRM API client Base service
 */
namespace Ufee\Amo\Base\Services;
use Ufee\Amo\Amoapi;
use Ufee\Amo\Models\Account;
use Ufee\Amo\Collections\QueryCollection;
	
/**
 * @property Amoapi $instance
 * @property Account $account
 * @property QueryCollection $queries
 */
class Service
{
	protected static $_service_instances = [];
	protected static $_require = [
		'add' => [],
		'update' => ['id', 'updated_at']
	];
	protected $account_id;
	protected $entity_key = 'entitys';
	protected $entity_model = '\Ufee\Amo\Base\Model';
	protected $methods = [];
	protected $api_args = [];
		
    /**
     * Constructor
	 * @param integer $account_id
     */
    private function __construct($account_id)
    {
        $this->account_id = $account_id;
		$this->_boot();
	}
	
    /**
     * Service on load
	 * @return void
     */
	protected function _boot()
	{
		
	}

    /**
     * Set service instance
	 * @param $name Service name
	 * @param Amoapi $instance
	 * @return Service
     */
    public static function setInstance($name, \Ufee\Amo\Amoapi &$instance)
    {
		if (is_null($name)) {
			$name = lcfirst(static::getBasename());
		}
		if (!isset(static::$_service_instances[$name])) {
			static::$_service_instances[$name] = new static($instance->getAuth('id'));
		}
		return static::getInstance($name);
	}
	
    /**
     * Get service instance
	 * @param $name Service name
	 * @return Service
     */
    public static function getInstance($name = null)
    {
		if (is_null($name)) {
			$name = lcfirst(static::getBasename());
		}
		if (!isset(static::$_service_instances[$name])) {
			return null;
		}
		return static::$_service_instances[$name];
	}

    /**
     * Get class basename
	 * @return string
     */
    public static function getBasename()
    {
        return substr(static::class, strrpos(static::class, '\\') + 1);
	}

    /**
     * Get api method
	 * @param string $target
     */
	public function __get($target)
	{
		if (isset($this->{$target})) {
			return $this->{$target};
		}
		if ($target === 'instance') {
			return Amoapi::getInstance($this->account_id);
		}
		if ($target === 'account') {
			return Amoapi::getInstance($this->account_id)->account;
		}
		if ($target === 'queries') {
			return Amoapi::getInstance($this->account_id)->queries->find('service', static::class);
		}
		if (!in_array($target, $this->methods)) {
			throw new \Exception('Invalid method called: '.$target);
		}
		$method_class = 'Ufee\\Amo\\Methods\\'.static::getBasename().'\\'.static::getBasename().ucfirst($target);
		return new $method_class($this);
	}
}
