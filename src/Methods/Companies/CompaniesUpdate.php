<?php
/**
 * amoCRM API Service method - update
 */
namespace Ufee\Amo\Methods\Companies;

class CompaniesUpdate extends \Ufee\Amo\Base\Methods\Post
{
	protected 
		$url = '/api/v2/companies',
		$content_type = 'json';
	
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
