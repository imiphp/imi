<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Annotation;

#[\Attribute()]
class Attr1
{
    public function __construct(public string $id = '', public array $arr = [])
    {
    }
}
