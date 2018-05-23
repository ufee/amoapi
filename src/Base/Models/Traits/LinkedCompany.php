<?php
/**
 * amoCRM Base trait - linked company
 */
namespace Ufee\Amo\Base\Models\Traits;

trait LinkedCompany
{
    /**
     * Set linked company
	 * @param mixed $company
     * @return bool
     */
    public function attachCompany($company)
    {
		if ($company_id = $this->getIdFrom($company)) {
			if ($company instanceof \Ufee\Amo\Models\Company) {
				$this->attributes['company'] = $company;
			}
			$this->company_id = $company_id;
		}
		return $this;
	}
	
    /**
     * Has linked company
	 * @param mixed $company
     * @return bool
     */
    public function hasCompany($company = null)
    {
		if ($company_id = $this->getIdFrom($company)) {
			return $this->company_id == $company_id;
		}
		return is_numeric($this->company_id) && $this->company_id > 0;
	}
	
	/**
     * Linked company get method
     * @return CompaniesList
     */
    public function company()
    {
		return $this->service->instance->companies()->where('id', $this->company_id);
	}
	
    /**
     * Protect company access
	 * @param mixed $company attribute
	 * @return Company|null
     */
    protected function company_access($company)
    {
		if (is_null($this->attributes['company'])) {
			if ($this->hasCompany()) {
				$this->attributes['company'] = $this->service->instance->companies()->find($this->company_id);
			}
		}
		return $this->attributes['company'];
	}
}
