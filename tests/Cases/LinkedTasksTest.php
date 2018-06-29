<?php
/**
 * amoCRM API tasks tests
 * @author Vlad Ionov <vlad@f5.com.ru>
 */
namespace Tests\Cases;

use PHPUnit\Framework\Assert;
use Ufee\Amo\Services;
	
require_once __DIR__ . '/../TestCase.php';

class LinkedTasksTest extends \Tests\TestCase
{
    public function testGetTasksService()
    {
		Assert::assertInstanceOf(
			Services\Tasks::class, $this->amo->tasks()
		);
    }

    public function testUpdateLeadTask()
    {
		$lead = $this->amo->leads()->create();
		$lead->name = 'Test UpdateLeadTask '.time();
		$lead->save();
		
		$task = $lead->createTask(1);
		$task->text = 'Test UpdateLeadTask '.time();
		$task->element_type = 2;
		$task->element_id = $lead->id;
		$has_created = $task->save();
		
		Assert::assertTrue(
			($has_created && is_numeric($task->id))
		);
		
		$task->text = 'Test UpdateLeadTask NEW';
		$task->save();
		$task = $this->amo->tasks()->find($task->id);
		
		Assert::assertEquals(
			$task->text, 'Test UpdateLeadTask NEW'
		);
    }
	
    public function testCreateTwoLeadTasks()
    {
		$lead = $this->amo->leads()->create();
		$lead->name = 'Test CreateTwoLeadTasks '.time();
		$lead->save();
		
		$create_tasks = [
			$lead->createTask(1),
			$lead->createTask(1)
		];
		$create_tasks[0]->text = 'Test CreateTwoLeadTasks 1 '.time();
		$create_tasks[1]->text = 'Test CreateTwoLeadTasks 2 '.time();
		$has_created = $this->amo->tasks()->add($create_tasks);
		
		Assert::assertTrue(
			($has_created && is_numeric($create_tasks[0]->id) && is_numeric($create_tasks[1]->id))
		);
    }
}
