<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Server\DataParser;

use Imi\Server\DataParser\IParser;
use Imi\Server\DataParser\JsonArrayParser;

class JsonArrayParserTest extends BaseDataParserTestCase
{
    protected function getParser(): IParser
    {
        return new JsonArrayParser();
    }

    /**
     * @return mixed
     */
    protected function getDecodeData()
    {
        return [
            'name'    => 'imi',
            'creator' => '宇润',
        ];
    }

    protected function getEncodeData(): string
    {
        return json_encode($this->getDecodeData(), \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);
    }
}
