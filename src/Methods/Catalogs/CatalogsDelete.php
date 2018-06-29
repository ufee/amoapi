<?php
/**
 * amoCRM API Service method - delete
 */
namespace Ufee\Amo\Methods\Catalogs;

class CatalogsDelete extends \Ufee\Amo\Base\Methods\Post
{
	protected 
		$url = '/api/v2/catalogs';
	
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
