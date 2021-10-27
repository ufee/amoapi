<?php
/**
 * amoCRM Account model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Collections;

class Account extends \Ufee\Amo\Base\Models\Model
{
	protected
		$system = [
			'id',
			'name',
			'subdomain',
			'currency',
			'timezone',
			'timezone_offset',
			'language',
			'date_pattern',	
		],
		$hidden = [
			'service',
			'currentUser',
			'users',
			'userGroups',
			'pipelines',
			'customFields',
			'noteTypes',
			'taskTypes',
		];
	
    /**
     * Constructor
	 * @param mixed $data
	 * @param Service $service
     */
    public function __construct($data, \Ufee\Amo\Base\Services\Service &$service)
    {
        foreach ($this->system as $field_key) {
			$this->attributes[$field_key] = null;
		}
        foreach ($this->hidden as $field_key) {
			$this->attributes[$field_key] = null;
		}
        foreach ($this->writable as $field_key) {
			$this->attributes[$field_key] = null;
		}
        foreach ($data as $field_key=>$val) {
			if (array_key_exists($field_key, $this->attributes)) {
				$this->attributes[$field_key] = $val;
			}
		}
		$this->attributes['service'] = $service;
		$this->_boot($data);
	}
	
    /**
     * Model on load
	 * @param array $data
	 * @return void
     */
    protected function _boot($data = [])
    {
		$client_id = $this->service->instance->getAuth('id');
		// группы пользователей
		$groups = [
			new \Ufee\Amo\Models\UserGroup(['id' => 0, 'name' => 'Отдел продаж', 'client_id' => $client_id])
		];
		foreach ($data->_embedded->groups as $id=>$group) {
			$group->client_id = $client_id;
			$groups[$id]= new \Ufee\Amo\Models\UserGroup($group);
		}
		$this->attributes['userGroups'] = new Collections\UserGroupCollection($groups);
		// пользователи
		$users = [];
		foreach ($data->_embedded->users as $id=>$user) {
			$users[$id]= new \Ufee\Amo\Models\User($user, $this->userGroups->byId($user->group_id));
		}
		$this->attributes['users'] = new Collections\UserCollection($users);
		$this->attributes['currentUser'] = $this->attributes['users']->get($data->current_user);
		
		// воронки
		$this->attributes['pipelines'] = new Collections\PipelineCollection(
			(array)$data->_embedded->pipelines, $this
		);
		// типы примечаний
		$this->attributes['noteTypes'] = new Collections\NoteTypesCollection(
			(array)$data->_embedded->note_types, $this
		);
		// типы задач
		$this->attributes['taskTypes'] = new Collections\TaskTypesCollection(
			(array)$data->_embedded->task_types, $this
		);
		$catalogCustomFields = [];
		if (isset($data->_embedded->custom_fields->catalogs)) {
			foreach ($data->_embedded->custom_fields->catalogs as $catalog_id=>$cfields) {
				$catalogCustomFields[$catalog_id] = (array)$cfields;
			}	
		}	
		// дополнительные поля
		$this->attributes['customFields'] = new AccountCustomFields(
			(array)$data->_embedded->custom_fields->companies, 
			(array)$data->_embedded->custom_fields->contacts, 
			(array)$data->_embedded->custom_fields->leads, 
			(array)$data->_embedded->custom_fields->customers, 
			$catalogCustomFields,
			$this
		);
	}
}
