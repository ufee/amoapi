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
		];
	
    /**
     * Constructor
	 * @param array $data
	 * @param UserGroup $group
	 * @return void
     */
    public function __construct($data = [], UserGroup $group)
    {
		parent::__construct($data);
		$this->attributes['phone'] = $data->phone_number;
		$this->attributes['group'] = $group;
	}
}
