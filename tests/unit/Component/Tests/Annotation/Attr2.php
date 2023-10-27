<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Annotation;

#[\Attribute()]
class Attr2
{
    public ?Attr1 $attr1 = null;

    /**
     * @param Attr1[] $attr1s
     */
    public function __construct(?Attr1 $attr1 = null, public array $attr1s = [])
    {
        // 为了测试覆盖，$attr1s 特意不用属性提升写法
        $this->attr1 = $attr1;
    }
}
