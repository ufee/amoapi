<?php
/**
 * amoCRM API leads tests
 * @author Vlad Ionov <vlad@f5.com.ru>
 */
namespace Tests\Cases;

use PHPUnit\Framework\Assert;
use Ufee\Amo\Services;
	
require_once __DIR__ . '/../TestCase.php';

class LeadsTest extends \Tests\TestCase
{
    public function testGetLeadsService()
    {
		Assert::assertInstanceOf(
			Services\Leads::class, $this->amo->leads()
		);
    }
	
    public function testCreateOneLead()
    {
		$model = $this->amo->leads()->create();
		$model->name = 'Test CreateOneLead '.time();
		$has_created = $model->save();

		Assert::assertTrue(
			($has_created && is_numeric($model->id))
		);
    }
	
    public function testUpdateLeadSale()
    {
		$model = $this->amo->leads()->create();
		$model->name = 'Test UpdateLeadSale '.time();
		$model->save();
		
		$model->sale = 123000;
		$model->save();
		$model = $this->amo->leads()->find($model->id);
		
		Assert::assertEquals(
			$model->sale, 123000
		);
    }
	
    public function testCreateTwoLeads()
    {
		$create_models = [
			$this->amo->leads()->create(),
			$this->amo->leads()->create()
		];
		$create_models[0]->name = 'Test CreateTwoLeads 1 '.time();
		$create_models[1]->name = 'Test CreateTwoLeads 2 '.time();
		$has_created = $this->amo->leads()->add($create_models);
		
		Assert::assertTrue(
			($has_created && is_numeric($create_models[0]->id) && is_numeric($create_models[0]->id))
		);
    }
}
