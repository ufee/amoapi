<?php
/**
 * amoCRM User model Collection class
 */
namespace Ufee\Amo\Collections;
use Ufee\Amo\Models\User;

class UserCollection extends \Ufee\Amo\Base\Collections\Collection
{
	/**
	 * Get user from id
	 * @param $id - user id
	 * @return User
	 */
	public function byId($id)
	{
		return $this->find('id', $id)->first();
	}
}
