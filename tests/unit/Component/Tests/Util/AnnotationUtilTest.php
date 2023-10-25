<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Bean\Util\AnnotationUtil;
use Imi\Test\BaseTest;

class AnnotationUtilTest extends BaseTest
{
    public function testGenerateAttributesCode(): void
    {
        $attributes = [
            new Attr1(),
            new Attr2(new Attr1(id: 'a\\1', arr: [1, 2, 3]), [
                new Attr1(id: 'b'),
                new Attr1(id: 'c'),
            ]),
        ];
        $this->assertEquals(<<<'CODE'
        #[
            \Imi\Test\Component\Tests\Util\Attr1(),
            \Imi\Test\Component\Tests\Util\Attr2(attr1: [
                new \Imi\Test\Component\Tests\Util\Attr1(id: 'a\\1', arr: [
                    1,
                    2,
                    3
                ])
            ], attr1s: [
                new \Imi\Test\Component\Tests\Util\Attr1(id: 'b'),
                new \Imi\Test\Component\Tests\Util\Attr1(id: 'c')
            ])
        ]
        CODE, AnnotationUtil::generateAttributesCode($attributes));
    }

    public function testGenerateAttributesCodeNotAttr(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Class Imi\Test\Component\Tests\Util\NotAttr does not an Attribute');
        AnnotationUtil::generateAttributesCode(new NotAttr());
    }
}

#[\Attribute()]
class Attr1
{
    public function __construct(public string $id = '', public array $arr = [])
    {
    }
}

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

class NotAttr
{
}
