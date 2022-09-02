<?php
/**
 * amoCRM Company model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Base\Models\Interfaces\LinkedContacts;
use Ufee\Amo\Base\Models\Interfaces\LinkedCustomers;
use Ufee\Amo\Base\Models\Interfaces\LinkedLeads;
use Ufee\Amo\Base\Models\Interfaces\LinkedNotes;
use Ufee\Amo\Base\Models\Interfaces\LinkedTags;
use Ufee\Amo\Base\Models\Interfaces\LinkedTasks;
use Ufee\Amo\Base\Models\Traits;

class Company extends \Ufee\Amo\Base\Models\ModelWithCF implements LinkedLeads,LinkedContacts,LinkedCustomers,LinkedTasks,LinkedNotes,LinkedTags
{
	use Traits\LinkedLeads, Traits\LinkedContacts, Traits\LinkedCustomers, Traits\LinkedTasks, Traits\LinkedNotes, Traits\EntityDetector, Traits\LinkedTags;

	protected static 
		$cf_category = 'companies',
		$_type = 'company',
		$_type_id = 3;
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
			'contacts',
			'customers',
			'notes',
			'tasks'
		],
		$writable = [
			'name',
			'created_at',
			'created_by',
			'responsible_user_id',
			'contacts_id',
			'leads_id',
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
		$this->attributes['leads_id'] = [];
		if (isset($data->leads->id)) {
			$this->attributes['leads_id'] = $data->leads->id;
		}
		$this->attributes['leads'] = null;

		$this->attributes['contacts_id'] = [];
		if (isset($data->contacts->id)) {
			$this->attributes['contacts_id'] = $data->contacts->id;
		}
		$this->attributes['contacts'] = null;

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
		$company = $this;
		$lead = $this->service->instance->leads()->create();
		$lead->responsible_user_id = $this->responsible_user_id;
		$lead->attachCompany($this);

		$lead->onCreate(function(&$model) use (&$company) {
			$company->attachLead($model);
		});
		return $lead;
	}

	/**
     * Create linked contact model
     * @return Contact
     */
    public function createContact()
    {
		$company = $this;
		$contact = $this->service->instance->contacts()->create();
		$contact->responsible_user_id = $this->responsible_user_id;
		$contact->attachCompany($this);

		$contact->onCreate(function(&$model) use (&$company) {
			$company->attachContact($model);
		});
		return $contact;
	}

	/**
     * Create linked customer model
     * @return Customer
     */
    public function createCustomer()
    {
		$company = $this;
		$customer = $this->service->instance->customers()->create();
		$customer->responsible_user_id = $this->responsible_user_id;
		$customer->attachCompany($this);

		$customer->onCreate(function(&$model) use (&$company) {
			$company->attachCustomer($model);
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
