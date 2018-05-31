<?php
/**
 * amoCRM API Service method - update
 */
namespace Ufee\Amo\Methods\Transactions;

class TransactionsUpdate extends \Ufee\Amo\Base\Methods\Post
{
	protected 
		$url = '/api/v2/transactions/comment';
	
    /**
     * Update entitys in CRM
	 * @param array $raws
	 * @param array $arg
     */
    public function update($raws, $arg = [])
    {
		return $this->call(['update' => $raws], $arg);
	}
}
