<?php
/**
 * amoCRM API client
 * @author Vlad Ionov <vlad@f5.com.ru>
 * @version 0.8
 */
namespace Ufee\Amo;

if (!defined('AMOAPI_ROOT')) {
	define('AMOAPI_ROOT', dirname(__FILE__));
}
if (!defined('CURL_SSLVERSION_TLSv1_2')) {
	define('CURL_SSLVERSION_TLSv1_2', 6);
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
 * @method \Ufee\Amo\Services\Ajax ajax()
 */
class ApiClient
{
	protected static $_instances = [];
	protected static $_queries = [];

	protected $services = [
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
		'ajax'
	];
	protected $_account;
	
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
		if (!$service = $service_class::getInstance($service_name, $this)) {
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
			return self::$_queries[$this->getAuth('id')];
		}
		if ($target === 'session') {
			return $this->session;
		}
		if (!in_array($target, $this->services)) {
			throw new \Exception('Invalid service called: '.$target);
		}
		$service_class = '\\Ufee\\Amo\\Services\\'.ucfirst($target);
		if (!$service = $service_class::getInstance($target, $this)) {
			$service = $service_class::setInstance($target, $this);
		}
		if (!method_exists($service, $target)) {
			throw new \Exception('Invalid service method called: '.$target.'()');
		}
		return $service->{$target}();
	}
}
