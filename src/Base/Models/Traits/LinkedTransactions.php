<?php
/**
 * amoCRM Base trait - linked transactions
 */
namespace Ufee\Amo\Base\Models\Traits;

trait LinkedTransactions
{
    /**
     * Attach leads
	 * @param array $leads
     * @return static
     */
    public function attachLeads($leads)
    {
		foreach ($leads as $lead) {
			$this->attachLead($lead);
		}
		return $this;
	}
	
    /**
     * Attach lead
	 * @param mixed $lead
     * @return static
     */
    public function attachLead($lead)
    {
		if ($lead_id = $this->getIdFrom($lead)) {
			
			$linked_leads = array_merge($this->leads_id, [$lead_id]);
			$this->leads_id = array_unique($linked_leads);
			
			if ($lead instanceof \Ufee\Amo\Models\Lead && !$this->leads->find('id', $lead->id)->first()) {
				$this->leads->push($lead);
			}
		}
		return $this;
	}
	
    /**
     * Has linked leads
	 * @param mixed $leads
     * @return bool
     */
    public function hasLeads($leads = null)
    {
		if (!is_null($leads)) {
			if (!is_array($leads)) {
				$leads = [$leads];
			}
			foreach ($leads as $lead) {
				if (!$lead_id = $this->getIdFrom($lead)) {
					return false;
				}
				if (!in_array($lead_id, $this->leads_id)) {
					return false;
				}
			}
			return true;
		}
		return count($this->leads_id) > 0;
	}

	/**
     * Linked transactions get method
     * @return LeadsList
     */
    public function transactions()
    {
		return $this->service->instance->transactions()->where('customer_id', $this->id);
	}
	
    /**
     * Protect leads access
	 * @param mixed $leads attribute
	 * @return LeadCollection
     */
    protected function leads_access($leads)
    {
		if (is_null($this->attributes['leads'])) {
			$service = $this->service->instance->leads();
			$this->attributes['leads'] = new \Ufee\Amo\Collections\LeadCollection([], $service);
		}
		if ($this->hasLeads()) {
			$can_load_leads = [];
			foreach ($this->leads_id as $lead_id) {
				if (!$this->attributes['leads']->find('id', $lead_id)->first()) {
					$can_load_leads[]= $lead_id;
				}
			}
			if (!empty($can_load_leads)) {
				$this->attributes['leads'] = $this->leads()->call();
			}
		}
		return $this->attributes['leads'];
	}
}
