<?php
/**
 * amoCRM API companies tests
 * @author Vlad Ionov <vlad@f5.com.ru>
 */
namespace Tests\Cases;

use PHPUnit\Framework\Assert;
use Ufee\Amo\Services;
	
require_once __DIR__ . '/../TestCase.php';

class CompaniesTest extends \Tests\TestCase
{
    public function testGetCompaniesService()
    {
		Assert::assertInstanceOf(
			Services\Companies::class, $this->amo->companies()
		);
    }
	
    public function testCreateOneCompany()
    {
		$model = $this->amo->companies()->create();
		$model->name = 'Test CreateOneCompany '.time();
		$has_created = $model->save();

		Assert::assertTrue(
			($has_created && is_numeric($model->id))
		);
    }
	
    public function testUpdateCompanyName()
    {
		$model = $this->amo->companies()->create();
		$model->name = 'Test UpdateCompany '.time();
		$model->save();
		
		$model->name = 'Test UpdateCompany NEW';
		$model->save();
		$model = $this->amo->companies()->find($model->id);
		
		Assert::assertEquals(
			$model->name, 'Test UpdateCompany NEW'
		);
    }
	
    public function testCreateTwoCompanies()
    {
		$create_models = [
			$this->amo->companies()->create(),
			$this->amo->companies()->create()
		];
		$create_models[0]->name = 'Test CreateTwoCompanies 1 '.time();
		$create_models[1]->name = 'Test CreateTwoCompanies 2 '.time();
		$has_created = $this->amo->companies()->add($create_models);
		
		Assert::assertTrue(
			($has_created && is_numeric($create_models[0]->id) && is_numeric($create_models[0]->id))
		);
    }
}
