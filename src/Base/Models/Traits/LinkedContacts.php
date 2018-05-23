<?php
/**
 * amoCRM Base trait - linked contacts
 */
namespace Ufee\Amo\Base\Models\Traits;

trait LinkedContacts
{
    /**
     * Attach contacts
	 * @param array $contacts
     * @return static
     */
    public function attachContacts($contacts)
    {
		foreach ($contacts as $contact) {
			$this->attachContact($contact);
		}
		return $this;
	}
	
    /**
     * Attach contact
	 * @param mixed $contact
     * @return static
     */
    public function attachContact($contact)
    {
		if ($contact_id = $this->getIdFrom($contact)) {
			
			$linked_contacts = array_merge($this->contacts_id, [$contact_id]);
			$this->contacts_id = array_unique($linked_contacts);
			
			if ($contact instanceof \Ufee\Amo\Models\Contact && !$this->contacts->find('id', $contact->id)->first()) {
				$this->contacts->push($contact);
			}
		}
		return $this;
	}
	
    /**
     * Has linked contacts
	 * @param mixed $contacts
     * @return bool
     */
    public function hasContacts($contacts = null)
    {
		if (!is_null($contacts)) {
			if (!is_array($contacts)) {
				$contacts = [$contacts];
			}
			foreach ($contacts as $contact) {
				if (!$contact_id = $this->getIdFrom($contact)) {
					return false;
				}
				if (!in_array($contact_id, $this->contacts_id)) {
					return false;
				}
			}
			return true;
		}
		return count($this->contacts_id) > 0;
	}
	
	/**
     * Linked contacts get method
     * @return ContactsList
     */
    public function contacts()
    {
		return $this->service->instance->contacts()->where('id', $this->contacts_id);
	}
	
    /**
     * Protect contacts access
	 * @param mixed $contacts attribute
	 * @return LeadCollection
     */
    protected function contacts_access($contacts)
    {
		if (is_null($this->attributes['contacts'])) {
			$service = $this->service->instance->contacts();
			$this->attributes['contacts'] = new \Ufee\Amo\Collections\ContactCollection([], $service);
		}
		if ($this->hasContacts()) {
			$can_load_contacts = [];
			foreach ($this->contacts_id as $contact_id) {
				if (!$this->attributes['contacts']->find('id', $contact_id)->first()) {
					$can_load_contacts[]= $contact_id;
				}
			}
			if (!empty($can_load_contacts)) {
				$this->attributes['contacts'] = $this->contacts()->call();
			}
		}
		return $this->attributes['contacts'];
	}
}
