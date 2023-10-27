<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Bean\Util\AttributeUtil;
use Imi\Test\BaseTest;
use Imi\Test\Component\Tests\Annotation\Attr1;
use Imi\Test\Component\Tests\Annotation\Attr2;
use Imi\Test\Component\Tests\Annotation\NotAttr;

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
            new Attr2(),
        ];
        $this->assertStringEqualsStringIgnoringLineEndings(<<<'CODE'
        #[
            \Imi\Test\Component\Tests\Annotation\Attr1(),
            \Imi\Test\Component\Tests\Annotation\Attr2(attr1: [
                new \Imi\Test\Component\Tests\Annotation\Attr1(id: 'a\\1', arr: [
                    1,
                    2,
                    3
                ])
            ], attr1s: [
                new \Imi\Test\Component\Tests\Annotation\Attr1(id: 'b'),
                new \Imi\Test\Component\Tests\Annotation\Attr1(id: 'c')
            ]),
            \Imi\Test\Component\Tests\Annotation\Attr2()
        ]
        CODE, AttributeUtil::generateAttributesCode($attributes));

        $this->assertStringEqualsStringIgnoringLineEndings(<<<'CODE'
        #[
            \Imi\Test\Component\Tests\Annotation\Attr1()
        ]
        CODE, AttributeUtil::generateAttributesCode(new Attr1()));
    }

    public function testGenerateAttributesCodeNotAttr(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Class Imi\Test\Component\Tests\Annotation\NotAttr does not an Attribute');
        AttributeUtil::generateAttributesCode(new NotAttr());
    }
}
