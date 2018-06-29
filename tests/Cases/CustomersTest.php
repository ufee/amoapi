<?php
/**
 * amoCRM API customers tests
 * @author Vlad Ionov <vlad@f5.com.ru>
 */
namespace Tests\Cases;

use PHPUnit\Framework\Assert;
use Ufee\Amo\Services;
	
require_once __DIR__ . '/../TestCase.php';

class CustomersTest extends \Tests\TestCase
{
    public function testGetCustomersService()
    {
		Assert::assertInstanceOf(
			Services\Customers::class, $this->amo->customers()
		);
    }
	
    public function testCustomersManage()
    {
		$model = $this->amo->customers()->create();
		$model->name = 'Test CreateCustomer '.time();
		$model->next_date = time();
		$model->next_price = 100;
		$has_created = $model->save();

		Assert::assertTrue(
			($has_created && is_numeric($model->id))
		);
		
		$customer_id = $model->id;
		$model->name = 'Test UpdateCustomer';
		$model->save();
		$customer = $this->amo->customers()->find($customer_id);
		
		Assert::assertTrue(
			($customer->name === $model->name)
		);
		
		$transaction = $customer->createTransaction();
		$transaction->price = 200;
		$has_created = $transaction->save();
		
		Assert::assertTrue(
			($has_created && is_numeric($transaction->id))
		);
		
		$transaction_id = $transaction->id;
		$transaction->comment = 'test';
		$transaction->save();
		$customerTransaction = $this->amo->transactions()->find($transaction_id);
		
		Assert::assertTrue(
			($customerTransaction->comment === $transaction->comment)
		);
		
		$transaction->delete();
		$customerTransaction = $this->amo->transactions()->find($transaction_id);
		
		Assert::assertTrue(
			($customerTransaction === null)
		);
		
		$model->delete();
		$customer = $this->amo->customers()->find($customer_id);
		
		Assert::assertTrue(
			($customer === null)
		);
    }
}
