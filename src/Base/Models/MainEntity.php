<?php
/**
 * amoCRM Base main model with Custom fiels
 */
namespace Ufee\Amo\Base\Models;
use Ufee\Amo\Models\EntityCustomFields,
	Ufee\Amo\Base\Collections\Collection;

class MainEntity extends ModelWithCF
{
	use Ufee\Amo\Base\Traits\LinkedTasks,
		Ufee\Amo\Base\Traits\LinkedNotes;
		
	protected 
		$hidden = [
			'service',
			'custom_fields',
			'customFields',
			'notes',
			'tasks'
		];
}
