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
     * Model on load
	 * @param array $data
	 * @return void
     */
    protected function _boot($data = [])
    {
		// группы пользователей
		$groups = [
			new \Ufee\Amo\Models\UserGroup(['id' => 0, 'name' => 'Отдел продаж'])
		];
		foreach ($data->_embedded->groups as $id=>$group) {
			$groups[$id]= new \Ufee\Amo\Models\UserGroup($group);
		}
		$this->attributes['userGroups'] = new Collections\UserGroupCollection($groups);
		// пользователи
		$users = [];
		foreach ($data->_embedded->users as $id=>$user) {
			$users[$id]= new \Ufee\Amo\Models\User($user, $this->userGroups->get($user->group_id));
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
