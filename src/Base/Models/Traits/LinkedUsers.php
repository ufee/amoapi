<?php
/**
 * amoCRM Base trait - linked users
 */
namespace Ufee\Amo\Base\Models\Traits;

trait LinkedUsers
{
    /**
     * Protect access created user
	 * @return User|null
     */
    protected function createdUser_access()
    {
		return $this->service->account->users->find('id', $this->attributes['created_by'])->first();
	}
	
    /**
     * Protect access responsible user
	 * @return User|null
     */
    protected function responsibleUser_access()
    {
		return $this->service->account->users->find('id', $this->attributes['responsible_user_id'])->first();
	}

    /**
     * Protect access updated user
	 * @return User|null
     */
    protected function updatedUser_access()
    {
		return $this->service->account->users->find('id', $this->attributes['updated_by'])->first();
	}
}
