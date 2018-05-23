<?php
/**
 * amoCRM API Service method - account current
 */
namespace Ufee\Amo\Methods\Account;
use Ufee\Amo\Api;

class AccountCurrent extends \Ufee\Amo\Base\Methods\Get
{
	protected 
		$url = '/api/v2/account';
	
    /**
     * Parse api response
	 * @param Query $query
	 * @return Collection
     */
    protected function parseResponse(\Ufee\Amo\Api\Query &$query)
    {
		if (!$data = $query->response->parseJson()) {
			throw new \Exception('Invalid API response (non JSON)');
		}
		if (!isset($data->id)) {
			throw new \Exception('Invalid API response (account: not found)');
		}
		return new \Ufee\Amo\Models\Account($data, $this->service);
	}
}
