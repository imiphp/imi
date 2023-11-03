<?php

declare(strict_types=1);

namespace Imi\Server\Http\SuperGlobals;

use Imi\Log\Log;
use Imi\RequestContext;

class Server implements \ArrayAccess, \JsonSerializable
{
    public function __construct(
        /**
         * 默认的 $_SERVER 变量.
         */
        private array $defaultServer
    ) {
    }

    public function offsetSet(mixed $key, mixed $value): void
    {
        $this->defaultServer[$key] = $value;
    }

    public function offsetExists(mixed $key): bool
    {
        if (isset($this->defaultServer[$key]))
        {
            return true;
        }
        /** @var \Imi\Server\Http\Message\Request|null $request */
        $request = RequestContext::get('request');
        if ($request)
        {
            $serverParams = $request->getServerParams();
        }
        else
        {
            $serverParams = &$this->defaultServer;
        }
        if (isset($serverParams[$key]) || isset($serverParams[strtolower((string) $key)]))
        {
            return true;
        }

        return false;
    }

    public function offsetUnset(mixed $key): void
    {
        Log::warning('imi does not support to unset values from $_SERVER');
    }

    public function offsetGet(mixed $key): mixed
    {
        /** @var \Imi\Server\Http\Message\Request|null $request */
        $request = RequestContext::get('request');
        if ($request)
        {
            $serverParams = $request->getServerParams();
        }
        else
        {
            $serverParams = &$this->defaultServer;
        }
        if (isset($serverParams[$key]))
        {
            return $serverParams[$key];
        }
        $lowerOffset = strtolower((string) $key);
        if (isset($serverParams[$lowerOffset]))
        {
            return $serverParams[$lowerOffset];
        }

        return null;
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
        /** @var \Imi\Server\Http\Message\Request|null $request */
        $request = RequestContext::get('request');
        if ($request)
        {
            $serverParams = $request->getServerParams();
        }
        else
        {
            $serverParams = &$this->defaultServer;
        }
        if ($serverParams)
        {
            return array_merge($this->defaultServer, array_change_key_case($serverParams, \CASE_UPPER));
        }
        else
        {
            return $this->defaultServer;
        }
    }
}
