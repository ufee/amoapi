<?php
/**
 * amoCRM User group model Collection class
 */
namespace Ufee\Amo\Collections;
use Ufee\Amo\Models\UserGroup;

class UserGroupCollection extends \Ufee\Amo\Base\Collections\Collection
{
	/**
	 * Get group by id
	 * @param $id - user group id
	 * @return UserGroup
	 */
	public function byId($id)
	{
        if (!$group = $this->find('id', $id)->first()) {
            $group = new UserGroup([
                'id' => '',
                'name' => '',
				'client_id' => ''
            ]);
        }
        return $group;
	}
}
