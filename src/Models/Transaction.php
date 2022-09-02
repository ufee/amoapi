<?php
/**
 * amoCRM Transaction model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Base\Models\Interfaces\EntityDetector;
use Ufee\Amo\Base\Models\Traits;

class Transaction extends \Ufee\Amo\Base\Models\ApiModel implements EntityDetector
{
	use Traits\EntityDetector;

	protected static 
		$cf_category = 'transactions',
		$_type = 'transaction',
		$_type_id = -1;
	protected
		$hidden = [
			'query_hash',
			'service',
			'customer'
		],
		$writable = [
			'customer_id',
			'date',
			'price',
			'comment',
			'next_date',
			'next_price',
			'elements'
		];
	
    /**
     * Model on load
	 * @param array $data
	 * @return void
     */
    protected function _boot($data = [])
    {
		parent::_boot($data);

		$this->attributes['customer_id'] = null;
		if (isset($data->customer->id)) {
			$this->attributes['customer_id'] = $data->customer->id;
		}
		$this->attributes['customer'] = null;
	}

    /**
     * Set linked customer
	 * @param mixed $customer
     * @return bool
     */
    public function attachCustomer($customer)
    {
		if ($customer_id = $this->getIdFrom($customer)) {
			if ($customer instanceof \Ufee\Amo\Models\Customer) {
				$this->attributes['customer'] = $customer;
			}
			$this->customer_id = $customer_id;
		}
		return $this;
	}

    /**
     * Protect customer access
	 * @param mixed $customer attribute
	 * @return Customer
     */
    public function customer_access($customer)
    {
		if (is_null($this->attributes['customer'])) {
			$this->attributes['customer'] = $this->service->instance->customers()->find($this->customer_id);
		}
		return $this->attributes['customer'];
	}

    /**
     * Get changed raw model api data
	 * @return array
     */
    public function getChangedRawApiData()
	{
		$data = $this->getChangedData();
		$data['transaction_id'] = $this->id;
		return $data;
	}

    /**
     * Delete model
     * @return array
     */
    public function delete()
    {
		return $this->service->delete($this);
    }

    /**
     * Convert Model to array
     * @return array
     */
    public function toArray()
    {
		$fields = parent::toArray();
		return $fields;
    }
}
