<?php
/**
 * amoCRM Base trait - linked main contact
 */
namespace Ufee\Amo\Base\Models\Traits;

trait MainContact
{
    /**
     * Has linked main contact
	 * @param mixed $contact
     * @return bool
     */
    public function hasMainContact($contact = null)
    {
		if ($contact_id = $this->getIdFrom($contact)) {
			return $this->main_contact_id == $contact_id;
		}
		return is_numeric($this->main_contact_id) && $this->main_contact_id > 0;
	}
	
    /**
     * Set linked main contact
	 * @param mixed $contact
     * @return bool
     */
    public function attachMainContact($contact)
    {
		$this->attachContact($contact);
		if ($contact_id = $this->getIdFrom($contact)) {
			if ($contact instanceof \Ufee\Amo\Models\Contact) {
				$this->attributes['contact'] = $contact;
			}
			$this->main_contact_id = $contact_id;
		}
		return $this;
	}
	
    /**
     * Protect main contact access
	 * @param mixed $contact attribute
	 * @return Contact|null
     */
    protected function contact_access($contact)
    {
		if (is_null($this->attributes['contact'])) {
			if ($this->hasMainContact()) {
				$this->attributes['contact'] = $this->contacts->find('id', $this->main_contact_id)->first();
			}
		}
		return $this->attributes['contact'];
	}
}
	