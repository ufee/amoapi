<?php
/**
 * amoCRM Lead model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Base\Models\Traits;

class Lead extends \Ufee\Amo\Base\Models\ModelWithCF
{
	use Traits\LinkedContacts, Traits\MainContact, Traits\LinkedCompany, Traits\LinkedTasks, Traits\LinkedNotes, Traits\LinkedPipeline, Traits\EntityDetector;

	protected static 
		$cf_category = 'leads',
		$_type = 'lead',
		$_type_id = 2;
	protected
		$hidden = [
			'query_hash',
			'service',
			'createdUser',
			'responsibleUser',
			'tags',
			'pipeline',
			'status',
			'customFields',
			'contacts',
			'contact',
			'company',
			'notes',
			'tasks'
		],
		$writable = [
			'name',
			'sale',
			'pipeline_id',
			'status_id',
			'created_at',
			'created_by',
			'responsible_user_id',
			'contacts_id',
			'main_contact_id',
			'company_id',
			'updated_at',
			'closed_at',
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

		if (isset($data->pipeline->id)) {
			$this->attributes['pipeline_id'] = $data->pipeline->id;
		}
		$this->attributes['tags'] = [];
		if (isset($data->tags)) {
			foreach ($data->tags as $tag) {
				$this->attributes['tags'][]= $tag->name;
			}			
		}
		$this->attributes['company_id'] = null;
		if (isset($data->company->id)) {
			$this->attributes['company_id'] = $data->company->id;
		}
		$this->attributes['company'] = null;

		$this->attributes['contacts_id'] = [];
		if (isset($data->contacts->id)) {
			$this->attributes['contacts_id'] = $data->contacts->id;
		}
		$this->attributes['contacts'] = null;

		$this->attributes['main_contact_id'] = null;
		if (isset($data->main_contact->id)) {
			$this->attributes['main_contact_id'] = $data->main_contact->id;
		}
		unset(
			$this->attributes['main_contact'], $this->attributes['pipeline']->_links
		);
	}

	/**
     * Create linked contact model
     * @return Contact
     */
    public function createContact()
    {
		$lead = $this;
		$contact = $this->service->instance->contacts()->create();
		$contact->attachLead($this);

		$contact->onCreate(function(&$model) use (&$lead) {
			$lead->attachContact($model);
		});
		return $contact;
	}

	/**
     * Create linked company model
     * @return Company
     */
    public function createCompany()
    {
		$lead = $this;
		$company = $this->service->instance->companies()->create();
		$company->attachLead($this);

		$company->onCreate(function(&$model) use (&$lead) {
			$lead->attachCompany($model);
		});
		return $company;
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
