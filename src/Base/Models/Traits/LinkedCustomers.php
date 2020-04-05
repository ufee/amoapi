<?php
/**
 * amoCRM Base trait - linked customers
 */
namespace Ufee\Amo\Base\Models\Traits;

trait LinkedCustomers
{
    /**
     * Attach customers
	 * @param array $customers
     * @return static
     */
    public function attachCustomers($customers)
    {
		foreach ($customers as $customer) {
			$this->attachCustomer($customer);
		}
		return $this;
	}
	
    /**
     * Attach customer
	 * @param mixed $customer
     * @return static
     */
    public function attachCustomer($customer)
    {
		if ($customer_id = $this->getIdFrom($customer)) {
			
			$linked_customers = array_merge($this->customers_id, [$customer_id]);
			$this->customers_id = array_unique($linked_customers);
			
			if ($customer instanceof \Ufee\Amo\Models\Customer && !$this->customers->find('id', $customer->id)->first()) {
				$this->customers->push($customer);
			}
		}
		return $this;
	}
	
    /**
     * Has linked customers
	 * @param mixed $customers
     * @return bool
     */
    public function hasCustomers($customers = null)
    {
		if (!is_null($customers)) {
			if (!is_array($customers)) {
				$customers = [$customers];
			}
			foreach ($customers as $customer) {
				if (!$customer_id = $this->getIdFrom($customer)) {
					return false;
				}
				if (!in_array($customer_id, $this->customers_id)) {
					return false;
				}
			}
			return true;
		}
		return count($this->customers_id) > 0;
	}

	/**
     * Linked customers get method
     * @return CustomersList
     */
    public function customers()
    {
		return $this->service->instance->customers()->where('id', $this->customers_id);
	}
	
    /**
     * Protect customers access
	 * @param mixed $customers attribute
	 * @return CustomerCollection
     */
    protected function customers_access($customers)
    {
		if (is_null($this->attributes['customers'])) {
			$service = $this->service->instance->customers();
			$this->attributes['customers'] = new \Ufee\Amo\Collections\CustomerCollection([], $service);
		}
		if ($this->hasCustomers()) {
			$can_load_customers = [];
			foreach ($this->customers_id as $customer_id) {
				if (!$this->attributes['customers']->find('id', $customer_id)->first()) {
					$can_load_customers[]= $customer_id;
				}
			}
			if (!empty($can_load_customers)) {
				$this->attributes['customers'] = $this->customers()->call();
			}
		}
		return $this->attributes['customers'];
	}
}
