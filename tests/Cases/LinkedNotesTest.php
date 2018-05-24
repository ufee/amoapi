<?php
/**
 * amoCRM API notes tests
 * @author Vlad Ionov <vlad@f5.com.ru>
 */
namespace Tests\Cases;

use PHPUnit\Framework\Assert;
use Ufee\Amo\Services;
	
require_once __DIR__ . '/../TestCase.php';

class LinkedNotesTest extends \Tests\TestCase
{
    public function testGetNotesService()
    {
		Assert::assertInstanceOf(
			Services\Notes::class, $this->amo->notes()
		);
    }
	
    public function testCreateOneLeadNote()
    {
		$lead = $this->amo->leads()->create();
		$lead->name = 'Test CreateOneLeadNote '.time();
		$lead->save();
		
		$note = $this->amo->notes()->create();
		$note->note_type = 4;
		$note->text = 'Test CreateOneLeadNote '.time();
		$note->element_type = 2;
		$note->element_id = $lead->id;
		$has_created = $note->save();

		Assert::assertTrue(
			($has_created && is_numeric($note->id))
		);
    }
	
    public function testUpdateLeadNote()
    {
		$lead = $this->amo->leads()->create();
		$lead->name = 'Test UpdateLeadNote '.time();
		$lead->save();
		
		$note = $lead->createNote(4);
		$note->text = 'Test UpdateLeadNote '.time();
		$note->element_type = 2;
		$note->element_id = $lead->id;
		$note->save();
		
		$note->text = 'Test UpdateLeadNote NEW';
		$note->save();
		$note = $this->amo->notes()->find($note->id);
		
		Assert::assertEquals(
			$note->text, 'Test UpdateLeadNote NEW'
		);
    }
	
    public function testCreateTwoLeadNotes()
    {
		$lead = $this->amo->leads()->create();
		$lead->name = 'Test CreateTwoLeadNotes '.time();
		$lead->save();
		
		$create_notes = [
			$lead->createNote(4),
			$lead->createNote(4)
		];
		$create_notes[0]->text = 'Test CreateTwoLeadNotes 1 '.time();
		$create_notes[1]->text = 'Test CreateTwoLeadNotes 2 '.time();
		$has_created = $this->amo->notes()->add($create_notes);
		
		Assert::assertTrue(
			($has_created && is_numeric($create_notes[0]->id) && is_numeric($create_notes[0]->id))
		);
    }
}
