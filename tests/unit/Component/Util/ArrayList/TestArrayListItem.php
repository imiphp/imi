<?php

declare(strict_types=1);

namespace Imi\Test\Component\Util\ArrayList;

class TestArrayListItem
{
    /**
     * @var mixed
     */
    public $id;

    /**
     * @var mixed
     */
    public $name;

    /**
     * @param mixed $id
     * @param mixed $name
     */
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
