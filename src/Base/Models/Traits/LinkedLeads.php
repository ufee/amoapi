<?php
/**
 * amoCRM Base trait - linked leads
 */
namespace Ufee\Amo\Base\Models\Traits;

trait LinkedLeads
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
     * Linked leads get method
     * @return LeadsList
     */
    public function leads()
    {
		return $this->service->instance->leads()->where('id', $this->leads_id);
	}
	
    /**
     * Protect leads access
	 * @param mixed $leads attribute
	 * @return LeadCollection
     */
    protected function leads_access($leads)
    {
		$service = $this->service->instance->leads();
		if (is_null($this->attributes['leads'])) {
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
				$chunks = array_chunk($this->leads_id, 400);
				$leads = new \Ufee\Amo\Collections\LeadCollection([], $service);

				foreach ($chunks as $chunk) {
					$part = $this->service->instance->leads()->where('id', $chunk)->call();
					$leads->merge($part);
				}
				$this->attributes['leads'] = $leads;
			}
		}
		return $this->attributes['leads'];
	}
}
