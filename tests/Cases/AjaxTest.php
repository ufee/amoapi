<?php
/**
 * amoCRM API ajax methods tests
 * @author Vlad Ionov <vlad@f5.com.ru>
 */
namespace Tests\Cases;

use PHPUnit\Framework\Assert;
use Ufee\Amo\Services;
	
require_once __DIR__ . '/../TestCase.php';

class AjaxTest extends \Tests\TestCase
{
    public function testGetAjaxCompaniesService()
    {
		$model = $this->amo->companies()->create();
		$result = $this->amo->ajax()->get('/ajax/contacts/list/', [
			'element_type' => 3
		]);
		Assert::assertTrue(
			(isset($result->response) && is_array($result->response->items))
		);
    }
}
