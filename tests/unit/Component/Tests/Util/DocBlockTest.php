<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Util;

use Imi\Test\BaseTest;
use Imi\Util\DocBlock;
use phpDocumentor\Reflection\DocBlock as RealDocBlock;
use phpDocumentor\Reflection\DocBlockFactory;

class DocBlockTest extends BaseTest
{
    public function testGetFactory(): void
    {
        $this->assertInstanceOf(DocBlockFactory::class, DocBlock::getFactory());
    }

    /**
     * test.
     */
    public function testGetDocBlock(): void
    {
        $block = DocBlock::getDocBlock(<<<'DATA'
        /**
         * @param int $a
         */
        DATA);
        $this->assertInstanceOf(RealDocBlock::class, $block);
        $tags = $block->getTags();
        $this->assertCount(1, $tags);
        $this->assertEquals('param', $tags[0]->getName());
    }
}
