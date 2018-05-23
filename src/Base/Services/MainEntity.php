<?php
/**
 * amoCRM API client Base service
 */
namespace Ufee\Amo\Base\Services;
use Ufee\Amo\Base\Collections\Collection;

class MainEntity extends LimitedList
{
	protected
		$methods = [
			'list', 'add', 'update', 'unlink'
		];
}
