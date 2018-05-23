<?php
/**
 * amoCRM API Service method - add
 */
namespace Ufee\Amo\Methods\Companies;

class CompaniesAdd extends \Ufee\Amo\Base\Methods\Post
{
	protected 
		$url = '/api/v2/companies';
	
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
