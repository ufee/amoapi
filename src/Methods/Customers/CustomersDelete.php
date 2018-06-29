<?php
/**
 * amoCRM API Service method - delete
 */
namespace Ufee\Amo\Methods\Customers;

class CustomersDelete extends \Ufee\Amo\Base\Methods\Post
{
	protected 
		$url = '/api/v2/customers';
	
    /**
     * Update entitys in CRM
	 * @param array $ids
	 * @param array $arg
     */
    public function delete($ids, $arg = [])
    {
		return $this->call(['delete' => $ids], $arg);
	}
}
