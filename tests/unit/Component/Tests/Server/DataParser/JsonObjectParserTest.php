<?php

declare(strict_types=1);

namespace Imi\Test\Component\Tests\Server\DataParser;

use Imi\Server\DataParser\IParser;
use Imi\Server\DataParser\JsonObjectParser;

class JsonObjectParserTest extends BaseDataParserTest
{
    protected function getParser(): IParser
    {
        return new JsonObjectParser();
    }

    /**
     * @return mixed
     */
    protected function getDecodeData()
    {
        $object = new \stdClass();
        $object->name = 'imi';
        $object->creator = '宇润';

        return $object;
    }

    protected function getEncodeData(): string
    {
        return json_encode($this->getDecodeData(), \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);
    }
}
