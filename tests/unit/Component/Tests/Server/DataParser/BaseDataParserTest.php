<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Server\DataParser;

use Imi\Server\DataParser\IParser;
use Imi\Test\BaseTest;

abstract class BaseDataParserTest extends BaseTest
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

    /**
     * @return mixed
     */
    abstract protected function getDecodeData();

    abstract protected function getEncodeData(): string;
}
