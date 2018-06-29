<?php
/**
 * amoCRM API catalogs tests
 * @author Vlad Ionov <vlad@f5.com.ru>
 */
namespace Tests\Cases;

use PHPUnit\Framework\Assert;
use Ufee\Amo\Services;
	
require_once __DIR__ . '/../TestCase.php';

class CatalogsTest extends \Tests\TestCase
{
    public function testGetCatalogsService()
    {
		Assert::assertInstanceOf(
			Services\Catalogs::class, $this->amo->catalogs()
		);
    }
	
    public function testCatalogManage()
    {
		$model = $this->amo->catalogs()->create();
		$model->name = 'Test CreateCatalog '.time();
		$has_created = $model->save();

		Assert::assertTrue(
			($has_created && is_numeric($model->id))
		);
		
		$catalog_id = $model->id;
		$model->name = 'Test UpdateCatalog';
		$model->save();
		$catalog = $this->amo->catalogs()->find($catalog_id);
		
		Assert::assertTrue(
			($catalog->name === $model->name)
		);
		
		$element = $catalog->createElement();
		$element->name = 'Test CreateElement';
		$has_created = $element->save();
		
		Assert::assertTrue(
			($has_created && is_numeric($element->id))
		);
		
		$element_id = $element->id;
		$element->name = 'Test UpdateElement';
		$element->save();
		$catalogEelement = $this->amo->catalogElements()->find($element_id);
		
		Assert::assertTrue(
			($catalogEelement->name === $element->name)
		);
		
		$element->delete();
		$catalogEelement = $this->amo->catalogElements()->find($element_id);
		
		Assert::assertTrue(
			($catalogEelement === null)
		);
		
		$model->delete();
		$catalog = $this->amo->catalogs()->find($catalog_id);
		
		Assert::assertTrue(
			($catalog === null)
		);
    }
}
