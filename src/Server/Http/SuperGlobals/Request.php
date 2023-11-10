<?php

declare(strict_types=1);

namespace Imi\Server\Http\SuperGlobals;

use Imi\Log\Log;
use Imi\RequestContext;

class Request implements \ArrayAccess, \JsonSerializable
{
    public function offsetSet(mixed $key, mixed $value): void
    {
        Log::warning('imi does not support to assign values to $_REQUEST');
    }

    public function offsetExists(mixed $key): bool
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');

        return null !== $request->request($key);
    }

    public function offsetUnset(mixed $key): void
    {
        Log::warning('imi does not support to unset values from $_REQUEST');
    }

    public function offsetGet(mixed $key): mixed
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');

        return $request->request($key);
    }

    public function __debugInfo(): array
    {
        return $this->jsonSerialize();
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');

        return $request->request();
    }
}
