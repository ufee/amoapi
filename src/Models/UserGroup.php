<?php
/**
 * amoCRM User group model
 */
namespace Ufee\Amo\Models;
use Ufee\Amo\Amoapi;
use Ufee\Amo\Oauthapi;

class UserGroup extends \Ufee\Amo\Base\Models\Model
{
	protected
		$system = [
			'id',
			'name',
			'client_id'
		];
		
    /**
     * Get Oauthapi instance
	 * @return Oauthapi
     */
    public function instance()
    {
		if (is_numeric($this->client_id)) {
			return Amoapi::getInstance($this->client_id);
		}
		return Oauthapi::getInstance($this->client_id);
	}
}
