<?php

declare(strict_types=1);

namespace Imi\SwooleTracker\Example\TCPServer\TCPServer\DataParser;

class JsonObjectFixedParser extends \Imi\Server\DataParser\JsonObjectParser
{
    /**
     * {@inheritDoc}
     */
    public function encode($data): string
    {
        $content = json_encode($data, \JSON_THROW_ON_ERROR);

        return pack('N', \strlen($content)) . $content;
    }

    /**
     * {@inheritDoc}
     */
    public function decode(string $data)
    {
        return json_decode(substr($data, 4));
    }
}
