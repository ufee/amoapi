<?php
/**
 * amoCRM API Service method - add
 */
namespace Ufee\Amo\Methods\Customers;

class CustomersAdd extends \Ufee\Amo\Base\Methods\Post
{
	protected 
		$url = '/api/v2/customers';
	
    /**
     * Add entitys to CRM
	 * @param array $raws
	 * @param array $arg
	 * @return 
     */
    public function add($raws, $arg = [])
    {
		return $this->call(['add' => $raws], $arg);
	}
}
