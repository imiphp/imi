<?php

declare(strict_types=1);

namespace Imi\Test\Component\Util\ArrayList;

class TestArrayListItem
{
    public mixed $id;

    public mixed $name;

    public function __construct(mixed $id, mixed $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
