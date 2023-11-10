<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Server\DataParser;

use Imi\Server\DataParser\IParser;
use Imi\Test\BaseTest;

abstract class BaseDataParserTestCase extends BaseTest
{
    public function test(): void
    {
        $parser = $this->getParser();
        $encodeData = $this->getEncodeData();
        $decodeData = $this->getDecodeData();
        $this->assertEquals($encodeData, $parser->encode($decodeData));
        $this->assertEquals($decodeData, $parser->decode($encodeData));
    }

    abstract protected function getParser(): IParser;

    abstract protected function getDecodeData(): mixed;

    abstract protected function getEncodeData(): string;
}
