<?php
/**
 * amoCRM API client Base service
 */
namespace Ufee\Amo\Base\Services;
use Ufee\Amo\ApiClient;
use Ufee\Amo\Amoapi;
use Ufee\Amo\Oauthapi;
use Ufee\Amo\Models\Account;
use Ufee\Amo\Collections\QueryCollection;
	
/**
 * @property ApiClient $instance
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
	protected $client_id;
	protected $entity_key = 'entitys';
	protected $entity_model = '\Ufee\Amo\Base\Model';
	protected $methods = [];
	protected $api_args = [];
		
    /**
     * Constructor
	 * @param integer $client_id
     */
    private function __construct($client_id)
    {
        $this->client_id = $client_id;
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
	 * @param ApiClient $instance
	 * @return Service
     */
    public static function setInstance($name, \Ufee\Amo\ApiClient &$instance)
    {
		if (is_null($name)) {
			$name = lcfirst(static::getBasename());
		}
		$key = $name.'-'.$instance->getAuth('id');
		if (!isset(static::$_service_instances[$key])) {
			static::$_service_instances[$key] = new static($instance->getAuth('id'));
		}
		return static::getInstance($name, $instance);
	}
	
    /**
     * Get service instance
	 * @param $name Service name
	 * @return Service
     */
    public static function getInstance($name = null, \Ufee\Amo\ApiClient &$instance)
    {
		if (is_null($name)) {
			$name = lcfirst(static::getBasename());
		}
		$key = $name.'-'.$instance->getAuth('id');
		if (!isset(static::$_service_instances[$key])) {
			return null;
		}
		return static::$_service_instances[$key];
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
		$apiClass = is_numeric($this->client_id) ? Amoapi::class : Oauthapi::class;
		if ($target === 'instance') {
			return $apiClass::getInstance($this->client_id);
		}
		if ($target === 'account') {
			return $apiClass::getInstance($this->client_id)->account;
		}
		if (!in_array($target, $this->methods)) {
			throw new \Exception('Invalid method called: '.$target);
		}
		$method_class = 'Ufee\\Amo\\Methods\\'.static::getBasename().'\\'.static::getBasename().ucfirst($target);
		return new $method_class($this);
	}
}
