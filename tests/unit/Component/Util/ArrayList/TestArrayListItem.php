<?php

namespace Imi\Test\Component\Util\ArrayList;

class TestArrayListItem
{
    public $id;

    public $name;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
