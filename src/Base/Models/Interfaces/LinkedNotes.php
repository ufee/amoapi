<?php

namespace Ufee\Amo\Base\Models\Interfaces;

interface LinkedNotes
{
    /**
     * Create linked note model
     * @return Note
     */
    public function createNote($type = 4);

    /**
     * Linked notes get method
     * @return NotesList
     */
    public function notes($type = null);

}