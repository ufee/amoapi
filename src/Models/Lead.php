<?php
/**
 * amoCRM Lead model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Base\Models\Traits;

class Lead extends \Ufee\Amo\Base\Models\ModelWithCF
{
	use Traits\LinkedContacts, Traits\MainContact, Traits\LinkedCompany, Traits\LinkedTasks, Traits\LinkedNotes, Traits\LinkedPipeline, Traits\EntityDetector, Traits\LinkedTags;

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
			'loss_reason_id',
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
			'updated_by',
			'closed_at',
			'closest_task_at',
			'created_user_id'
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
		$contact->responsible_user_id = $this->responsible_user_id;
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
		$company->responsible_user_id = $this->responsible_user_id;
		$company->attachLead($this);

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
