<?php

namespace Ufee\Amo\Base\Models\Interfaces;

interface LinkedTasks
{
    /**
     * Create linked task model
     * @return Task
     */
    public function createTask($type = 1);

    /**
     * Linked tasks get method
     * @return TasksList
     */
    public function tasks();

}