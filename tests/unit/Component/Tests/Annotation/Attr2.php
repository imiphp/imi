<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Annotation;

#[\Attribute()]
class Attr2
{
    /**
     * @param Attr1[] $attr1s
     */
    public function __construct(public Attr1 $attr1, public array $attr1s)
    {
    }
}
