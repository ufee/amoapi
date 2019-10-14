<?php
/**
 * amoCRM Base trait - linked parents for task,note
 */
namespace Ufee\Amo\Base\Models\Traits;

trait LinkedParents
{
    /**
     * Has linked contact
	 * @param mixed $contact
     * @return bool
     */
    public function hasLinkedContact($contact = null)
    {
		if (!is_null($contact)) {
			if (!$contact_id = $this->getIdFrom($contact)) {
				return false;
			}
			if ($contact_id != $this->element_id) {
				return false;
			}
		}
		return intval($this->element_type) === 1;
    }
    
    /**
     * Has linked lead
	 * @param mixed $lead
     * @return bool
     */
    public function hasLinkedLead($lead = null)
    {
		if (!is_null($lead)) {
			if (!$lead_id = $this->getIdFrom($lead)) {
				return false;
			}
			if ($lead_id != $this->element_id) {
				return false;
			}
		}
		return intval($this->element_type) === 2;
    }
    
    /**
     * Has linked company
	 * @param mixed $company
     * @return bool
     */
    public function hasLinkedCompany($company = null)
    {
		if (!is_null($company)) {
			if (!$company_id = $this->getIdFrom($company)) {
				return false;
			}
			if ($company_id != $this->element_id) {
				return false;
			}
		}
		return intval($this->element_type) === 3;
	}
    
    /**
     * Protect linkedContact access
	 * @param mixed $linkedContact attribute
	 * @return ContactCollection
     */
    protected function linkedContact_access($linkedContact)
    {
		if (intval($this->element_type) !== 1) {
			throw new \Exception('Invalid element type: '.$this->element_type.', must be 1');
		}
		if (is_null($this->attributes['linkedContact'])) {
			$this->attributes['linkedContact'] = $this->service->instance->contacts()->find($this->element_id);
		}
		return $this->attributes['linkedContact'];
	}
	
    /**
     * Protect linkedLead access
	 * @param mixed $linkedLead attribute
	 * @return LeadCollection
     */
    protected function linkedLead_access($linkedLead)
    {
		if (intval($this->element_type) !== 2) {
			throw new \Exception('Invalid element type: '.$this->element_type.', must be 2');
		}
		if (is_null($this->attributes['linkedLead'])) {
			$this->attributes['linkedLead'] = $this->service->instance->leads()->find($this->element_id);
		}
		return $this->attributes['linkedLead'];
    }
    
    /**
     * Protect linkedCompany access
	 * @param mixed $linkedCompany attribute
	 * @return CompanyCollection
     */
    protected function linkedCompany_access($linkedCompany)
    {
		if (intval($this->element_type) !== 3) {
			throw new \Exception('Invalid element type: '.$this->element_type.', must be 3');
		}
		if (is_null($this->attributes['linkedCompany'])) {
			$this->attributes['linkedCompany'] = $this->service->instance->companies()->find($this->element_id);
		}
		return $this->attributes['linkedCompany'];
	}
}
