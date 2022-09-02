<?php
/**
 * amoCRM Contact model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Base\Models\Interfaces\EntityDetector;
use Ufee\Amo\Base\Models\Interfaces\LinkedCompany;
use Ufee\Amo\Base\Models\Interfaces\LinkedCustomers;
use Ufee\Amo\Base\Models\Interfaces\LinkedLeads;
use Ufee\Amo\Base\Models\Interfaces\LinkedNotes;
use Ufee\Amo\Base\Models\Interfaces\LinkedTags;
use Ufee\Amo\Base\Models\Interfaces\LinkedTasks;
use Ufee\Amo\Base\Models\Traits;

class Contact extends \Ufee\Amo\Base\Models\ModelWithCF implements LinkedLeads,LinkedCompany,LinkedCustomers,LinkedTasks,LinkedNotes,EntityDetector,LinkedTags
{
	use Traits\LinkedLeads, Traits\LinkedCompany, Traits\LinkedCustomers, Traits\LinkedTasks, Traits\LinkedNotes, Traits\EntityDetector, Traits\LinkedTags;

	protected static 
		$cf_category = 'contacts',
		$_type = 'contact',
		$_type_id = 1;
	protected
		$hidden = [
			'query_hash',
			'service',
			'createdUser',
			'responsibleUser',
			'updatedUser',
			'tags',
			'leads',
			'customFields',
			'company_name',
			'company',
			'customers',
			'notes',
			'tasks'
		],
		$writable = [
			'name',
			'first_name',
			'last_name',
			'created_at',
			'created_by',
			'responsible_user_id',
			'leads_id',
			'company_id',
			'customers_id',
			'updated_at',
			'updated_by',
			'closest_task_at'
		];

    /**
     * Model on load
	 * @param array $data
	 * @return void
     */
    protected function _boot($data = [])
    {
		parent::_boot($data);

		$this->attributes['tags'] = [];
		if (isset($data->tags)) {
			foreach ($data->tags as $tag) {
				$this->attributes['tags'][]= $tag->name;
			}
		}
		$this->attributes['company_id'] = null;
		if (isset($data->company->id)) {
			$this->attributes['company_id'] = $data->company->id;
			$this->attributes['company_name'] = $data->company->name;
		}
		$this->attributes['company'] = null;

		$this->attributes['leads_id'] = [];
		if (isset($data->leads->id)) {
			$this->attributes['leads_id'] = $data->leads->id;
		}
		$this->attributes['leads'] = null;
		
		$this->attributes['customers_id'] = [];
		if (isset($data->customers->id)) {
			$this->attributes['customers_id'] = $data->customers->id;
		}
		$this->attributes['customers'] = null;
	}

	/**
     * Create linked lead model
     * @return Lead
     */
    public function createLead()
    {
		$contact = $this;
		$lead = $this->service->instance->leads()->create();
		$lead->responsible_user_id = $this->responsible_user_id;
		$lead->attachContact($this);

		if ($this->hasCompany()) {
			$lead->attachCompany($this->company_id);
		}
		$lead->onCreate(function(&$model) use (&$contact) {
			$contact->attachLead($model);
		});
		return $lead;
	}

	/**
     * Create linked company model
     * @return Company
     */
    public function createCompany()
    {
		$contact = $this;
		$company = $this->service->instance->companies()->create();
		$company->responsible_user_id = $this->responsible_user_id;
		$company->attachContact($this);

		$company->onCreate(function(&$model) use (&$contact) {
			$contact->attachCompany($model);
		});
		return $company;
	}

	/**
     * Create linked customer model
     * @return Customer
     */
    public function createCustomer()
    {
		$contact = $this;
		$customer = $this->service->instance->customers()->create();
		$customer->responsible_user_id = $this->responsible_user_id;
		$customer->attachContact($this);

		$customer->onCreate(function(&$model) use (&$contact) {
			$contact->attachCustomer($model);
		});
		return $customer;
	}

    /**
     * Convert Model to array
     * @return array
     */
    public function toArray()
    {
		$fields = parent::toArray();
		$fields['tags'] = $this->attributes['tags'];
		return $fields;
    }
}
