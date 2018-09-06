<?php
/**
 * amoCRM API client
 * @author Vlad Ionov <vlad@f5.com.ru>
 * @version 0.7
 */
namespace Ufee\Amo;

if (!defined('AMOAPI_ROOT')) {
	define('AMOAPI_ROOT', dirname(__FILE__));
}
/**
 * @property \Ufee\Amo\Collections\QueryCollection $queries
 * @property \Ufee\Amo\Models\Account $account
 * @property \Ufee\Amo\Collections\LeadCollection $leads
 * @property \Ufee\Amo\Collections\ContactCollection $contacts
 * @property \Ufee\Amo\Collections\CompanyCollection $companies
 * @property \Ufee\Amo\Collections\TaskCollection $tasks
 * @property \Ufee\Amo\Collections\NoteCollection $notes
 * @property \Ufee\Amo\Collections\CustomerCollection $customers
 * @property \Ufee\Amo\Collections\TransactionsCollection $transactions
 * @property \Ufee\Amo\Collections\CatalogCollection $catalogs
 * @property \Ufee\Amo\Collections\CatalogElementCollection $catalogElements
 * @property \Ufee\Amo\Collections\WebhookCollection $webhooks
 * @method \Ufee\Amo\Services\Account account()
 * @method \Ufee\Amo\Services\Leads leads()
 * @method \Ufee\Amo\Services\Contacts contacts()
 * @method \Ufee\Amo\Services\Companies companies()
 * @method \Ufee\Amo\Services\Tasks tasks()
 * @method \Ufee\Amo\Services\Notes notes()
 * @method \Ufee\Amo\Services\Customers customers()
 * @method \Ufee\Amo\Services\Transactions transactions()
 * @method \Ufee\Amo\Services\Catalogs catalogs()
 * @method \Ufee\Amo\Services\CatalogElements catalogElements()
 * @method \Ufee\Amo\Services\Webhooks webhooks()
 */
class Amoapi
{
	private static $_instances = [];
	private $services = [
		'account',
		'leads',
		'contacts',
		'companies',
		'tasks',
		'notes',
		'customers',
		'transactions',
		'catalogs',
		'catalogElements',
		'webhooks',
	];
	private $_account;
	private $_queries;
	
    /**
     * Constructor
	 * @param array $account
     */
    private function __construct(Array $account)
    {
		$this->_account = $account;
    }
	
    /**
     * Get account auth data
	 * @param string|null $key
	 * @return array
     */
    public function getAuth($key = null)
    {
		if (!is_null($key) && isset($this->_account[$key])) {
			return $this->_account[$key];
		}
		return $this->_account;
	}

	/**
     * Set account instance
	 * @param array $data
	 * @return Amoapi
     */
    public static function setInstance(Array $data)
    {
        if (empty($data)) {
            throw new \Exception('Incorrect amoCRM account data');
		}
		if (empty($data['zone'])) {
			$data['zone'] = 'ru';
		}
		if (empty($data['lang'])) {
			$data['lang'] = $data['zone'] == 'ru' ? 'ru' : 'en';
		}
		if (empty($data['timezone'])) {
			$data['timezone'] = 'Europe/Moscow';
		}
		$account = [
			'id' => '', 'domain' => '', 'login' => '', 'hash' => '', 'zone' => '', 'lang' => '', 'timezone' => ''
		];
		foreach ($account as $key=>$val) {
			if (!isset($data[$key])) {
				throw new \Exception('Incorrect account field: '.$key);
			}
			$account[$key] = $data[$key];
		}
        if (!isset(self::$_instances[$account['id']])) {
            self::$_instances[$account['id']] = new static($account);
		}
		$instance = self::getInstance($account['id']);
		$instance->_queries = new Collections\QueryCollection();
		$instance->_queries->boot($instance);
        return $instance;
	}

    /**
     * Get account instance
	 * @param integer $account_id
	 * @return Amoapi
     */
    public static function getInstance($account_id)
    {
		if (!is_numeric($account_id)) {
			throw new \Exception('Account id must be numeric: '.$daaccount_idta);
		}
		if (!isset(self::$_instances[$account_id])) {
			throw new \Exception('Account not found: '.$account_id);
		}
		return self::$_instances[$account_id];
	}
	
    /**
     * Call Service Methods
	 * @param string $service_name
	 * @param array $args
     */
	public function __call($service_name, $args)
	{
		if (!in_array($service_name, $this->services)) {
			throw new \Exception('Invalid service called: '.$service_name);
		}
		$service_class = '\\Ufee\\Amo\\Services\\'.ucfirst($service_name);
		if (!$service = $service_class::getInstance($service_name)) {
			$service = $service_class::setInstance($service_name, $this);
		}
		return $service;
	}
	
    /**
     * Get Service
	 * @param string $target
     */
	public function __get($target)
	{
		if ($target === 'queries') {
			return $this->_queries;
		}
		if (!in_array($target, $this->services)) {
			throw new \Exception('Invalid service called: '.$target);
		}
		$service_class = '\\Ufee\\Amo\\Services\\'.ucfirst($target);
		if (!$service = $service_class::getInstance($target)) {
			$service = $service_class::setInstance($target, $this);
		}
		if (!method_exists($service, $target)) {
			throw new \Exception('Invalid service method called: '.$target.'()');
		}
		return $service->{$target}();
	}
}
