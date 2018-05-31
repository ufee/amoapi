<?php
/**
 * amoCRM API Service method - update
 */
namespace Ufee\Amo\Methods\Customers;

class CustomersUpdate extends \Ufee\Amo\Base\Methods\Post
{
	protected 
		$url = '/api/v2/customers';
	
    /**
     * Update entitys in CRM
	 * @param array $raws
	 * @param array $arg
	 * @return 
     */
    public function update($raws, $arg = [])
    {
		return $this->call(['update' => $raws], $arg);
	}
}
