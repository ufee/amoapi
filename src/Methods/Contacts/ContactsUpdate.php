<?php
/**
 * amoCRM API Service method - update
 */
namespace Ufee\Amo\Methods\Contacts;

class ContactsUpdate extends \Ufee\Amo\Base\Methods\Post
{
	protected 
		$url = '/api/v2/contacts';
	
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
