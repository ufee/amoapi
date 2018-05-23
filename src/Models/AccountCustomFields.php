<?php
/**
 * amoCRM Model Custom fields class
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Collections;

class AccountCustomFields
{
    protected 
        $attributes = [];

    /**
     * Constructor
	 * @param array $cf_company_elems
	 * @param array $cf_contacts_elems
     * @param array $cf_leads_elems
	 * @param Account $account
     */
    public function __construct(Array $cf_company_elems = [], Array $cf_contacts_elems = [], Array $cf_leads_elems = [], \Ufee\Amo\Models\Account &$account)
    {
		$this->attributes['companies'] = new Collections\CompaniesCustomFieldCollection($cf_company_elems, $account);
		$this->attributes['contacts'] = new Collections\ContactsCustomFieldCollection($cf_contacts_elems, $account);
		$this->attributes['leads'] = new Collections\LeadsCustomFieldCollection($cf_leads_elems, $account);
		$this->attributes['account'] = $account;
    }
    
    /**
     * Protect get fields
	 * @param string $field
     */
	public function __get($field)
	{
		if (!array_key_exists($field, $this->attributes)) {
			throw new \Exception('Invalid '.static::class.' attribute: '.$field);
		}
		return $this->attributes[$field];
	}
}
