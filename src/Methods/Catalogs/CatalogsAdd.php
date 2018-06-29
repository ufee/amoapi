<?php
/**
 * amoCRM API Service method - add
 */
namespace Ufee\Amo\Methods\Catalogs;

class CatalogsAdd extends \Ufee\Amo\Base\Methods\Post
{
	protected 
		$url = '/api/v2/catalogs';
	
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
