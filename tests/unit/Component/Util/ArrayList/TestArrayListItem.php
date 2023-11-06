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

    public function __construct(mixed $id, mixed $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
