<?php
/**
 * amoCRM User model
 */
namespace Ufee\Amo\Models;

class User extends \Ufee\Amo\Base\Models\Model
{
	protected
		$system = [
			'id',
			'name',
			'last_name',
			'login',
			'phone',
			'photo',
			'language',
			'group_id',
			'is_active',
			'is_free',
			'is_admin',
			'rights'
		],
		$hidden = [
			'group'
		],
		$_activity = [];
	
    /**
     * Constructor
	 * @param array $data
	 * @param UserGroup $group
	 * @return void
     */
    public function __construct($data, UserGroup $group)
    {
		parent::__construct($data);
		
		$this->attributes['phone'] = $data->phone_number;
		$this->attributes['group'] = $group;
	}
	
    /**
     * Get user activity
	 * @param string $from - date start d.m.Y
	 * @param string|null $to - date end d.m.Y
	 * @return object
     */
    public function getActivity($from, $to = null)
    {
		if (is_null($to)) {
			$to = $from;
		}
		$key = $from.'.'.$to;
		if (!isset($this->_activity[$key])) {
			$result = $this->group->instance()->ajax()->get('/ajax/stats/by_activities/'.$this->id.'/', [
				'from' => $from,
				'to' => $to
			]);
			if (is_object($result) && property_exists($result, 'response')) {
				$result->response->tasks->total_size = $result->response->tasks_count;
				$this->_activity[$key] = (object)[
					'leads' => $result->response->leads,
					'contacts' => $result->response->contacts,
					'tasks' => $result->response->tasks,
					'notes' => $result->response->notes,
					'customers' => $result->response->customers
				];
				$result = null;
			}
		}
		return $this->_activity[$key];
	}
}
