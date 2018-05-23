<?php
/**
 * amoCRM Customer model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Base\Models\Traits;

class Customer extends \Ufee\Amo\Base\Models\MainEntity
{
	use Traits\LinkedContacts, Traits\LinkedCompany, Traits\LinkedNotes;

	protected static 
		$cf_category = 'customers',
		$_type = 'customer',
		$_type_id = 12;
	protected
		$hidden = [
			'query_hash',
			'service',
			'createdUser',
			'responsibleUser',
			'tags',
			'customFields',
			'company',
			'contacts',
			'notes'
		],
		$writable = [
			'name',
			'created_at',
			'created_by',
			'responsible_user_id',
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
		if (isset($data->leads)) {
			$this->attributes['leads']->entity = null;
		}
		if (isset($data->contacts)) {
			$this->attributes['contacts']->entity = null;
		}
		unset(
			$this->attributes['contacts']->_links, $this->attributes['leads']->_links, $this->attributes['customers']->_links
		);
	}

    /**
     * Has linked leads
     * @return bool
     */
    public function hasLeads()
    {
		return isset($this->attributes['leads']->id);
	}

    /**
     * Has linked contacts
     * @return bool
     */
    public function hasContacts()
    {
		return isset($this->attributes['contacts']->id);
	}

    /**
     * Get linked leads
	 * @param bool $force
     * @return LeadCollection|null
     */
    public function getLeads($force = false)
    {
		if (!isset($this->attributes['leads']->id)) {
			return null;
		}
		if (is_null($this->attributes['leads']->entity) || $force) {
			$this->attributes['leads']->entity = $this->service->instance->leads()->find($this->attributes['leads']->id);
		}
		return $this->attributes['leads']->entity;
	}

    /**
     * Get linked contacts
	 * @param bool $force
     * @return ContactCollection|null
     */
    public function getContacts($force = false)
    {
		if (!isset($this->attributes['contacts']->id)) {
			return null;
		}
		if (is_null($this->attributes['contacts']->entity) || $force) {
			$this->attributes['contacts']->entity = $this->service->instance->contacts()->find($this->attributes['contacts']->id);
		}
		return $this->attributes['contacts']->entity;
	}

    /**
     * Convert Model to array
     * @return array
     */
    public function toArray()
    {
		$fields = parent::toArray();
		$fields['contacts_id'] = [];
		if (isset($this->attributes['contacts']->id)) {
			$fields['contacts_id'] = $this->contacts->id;
		}
		$fields['leads_id'] = [];
		if (isset($this->attributes['leads']->id)) {
			$fields['leads_id'] = $this->leads->id;
		}
		$fields['tags'] = $this->attributes['tags'];
		return $fields;
    }
}
