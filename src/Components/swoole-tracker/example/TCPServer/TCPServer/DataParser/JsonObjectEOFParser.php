<?php

declare(strict_types=1);

namespace Imi\SwooleTracker\Example\TCPServer\TCPServer\DataParser;

class JsonObjectEOFParser extends \Imi\Server\DataParser\JsonObjectParser
{
    /**
     * {@inheritDoc}
     */
    public function encode($data): string
    {
        return json_encode($data) . "\r\n";
    }
}
