<?php
/**
 * amoCRM Lead model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Base\Models\Interfaces\EntityDetector;
use Ufee\Amo\Base\Models\Interfaces\LinkedCatalogElements;
use Ufee\Amo\Base\Models\Interfaces\LinkedCompany;
use Ufee\Amo\Base\Models\Interfaces\LinkedContacts;
use Ufee\Amo\Base\Models\Interfaces\LinkedNotes;
use Ufee\Amo\Base\Models\Interfaces\LinkedTags;
use Ufee\Amo\Base\Models\Interfaces\LinkedTasks;
use Ufee\Amo\Base\Models\Interfaces\MainContact;
use Ufee\Amo\Base\Models\Traits;

class Lead extends \Ufee\Amo\Base\Models\ModelWithCF implements LinkedContacts, MainContact, LinkedCompany, LinkedTasks, LinkedNotes, EntityDetector, LinkedTags, LinkedCatalogElements
{
	use Traits\LinkedContacts, Traits\MainContact, Traits\LinkedCompany, Traits\LinkedTasks, Traits\LinkedNotes, Traits\LinkedPipeline, Traits\EntityDetector, Traits\LinkedTags, Traits\LinkedCatalogElements;

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
			'updatedUser',
			'tags',
			'pipeline',
			'status',			
			'customFields',
			'contacts',
			'contact',
			'company_name',
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
			'catalog_elements_id',
			'updated_at',
			'updated_by',
			'closed_at',
			'closest_task_at',
			'visitor_uid',
			'loss_reason_id',
			'loss_reason_name',
			'catalog_elements_links'
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
			$this->attributes['company_name'] = $data->company->name;
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

		$this->attributes['catalog_elements_id'] = [];
		if (isset($data->catalog_elements->id)) {
			$this->attributes['catalog_elements_id'] = $data->catalog_elements->id;
		}
		$this->attributes['contacts'] = null;
		
		$this->attributes['catalog_elements_links'] = [];
		if (isset($data->_embedded->catalog_elements_links)) {
			foreach ($data->_embedded->catalog_elements_links as $catalog_element_link) {
				$this->attributes['catalog_elements_links'][$catalog_element_link->id] = $catalog_element_link;
			}
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
		$contact->responsible_user_id = $this->responsible_user_id;
		$contact->attachLead($this);

		if ($this->hasCompany()) {
			$contact->attachCompany($this->company_id);
		}
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
		$company->responsible_user_id = $this->responsible_user_id;
		$company->attachLead($this);

		if ($this->hasMainContact()) {
			$company->attachContact($this->main_contact_id);
		}
		$company->onCreate(function(&$model) use (&$lead) {
			$lead->attachCompany($model);
		});
		return $company;
	}

	/**
     * Set lead pipeline
	 * @param integer|Pipeline $pipeline
     * @return Lead
     */
    public function setPipeline($pipeline)
    {
		if (is_numeric($pipeline)) {
			$pipeline = $this->service->account->pipelines->byId($pipeline);
		}
		if (!$pipeline instanceof Pipeline) {
			throw new \Exception('Invalid pipeline');
		}
		$this->pipeline_id = $pipeline->id;
		$this->attributes['pipeline'] = $pipeline;
		return $this;
	}

	/**
     * Set lead status
	 * @param integer|PipelineStatus $status
     * @return Lead
     */
    public function setStatus($status)
    {
		if (is_numeric($status)) {
			$status = $this->pipeline->statuses->byId($status);
		}
		if (!$status instanceof PipelineStatus) {
			throw new \Exception('Invalid pipeline status');
		}
		$this->status_id = $status->id;
		$this->attributes['status'] = $status;
		return $this;
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
