<?php
/**
 * amoCRM API Service method - delete
 */
namespace Ufee\Amo\Methods\CatalogElements;

class CatalogElementsDelete extends \Ufee\Amo\Base\Methods\Post
{
	protected 
		$url = '/api/v2/catalog_elements';
	
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
