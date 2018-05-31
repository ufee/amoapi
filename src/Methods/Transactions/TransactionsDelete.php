<?php
/**
 * amoCRM API Service method - delete
 */
namespace Ufee\Amo\Methods\Transactions;

class TransactionsDelete extends \Ufee\Amo\Base\Methods\Post
{
	protected 
		$url = '/api/v2/transactions';
	
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
