<?php

declare(strict_types=1);

namespace Imi\Server\Http\SuperGlobals;

use Imi\Log\Log;
use Imi\RequestContext;

class Server implements \ArrayAccess, \JsonSerializable
{
    /**
     * 默认的 $_SERVER 变量.
     */
    private array $defaultServer = [];

    public function __construct(array $defaultServer)
    {
        $this->defaultServer = $defaultServer;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->defaultServer[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        if (isset($this->defaultServer[$offset]))
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
        if (isset($serverParams[$offset]) || isset($serverParams[strtolower($offset)]))
        {
            return true;
        }

        return false;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        Log::warning('imi does not support to unset values from $_SERVER');
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
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
        if (isset($serverParams[$offset]))
        {
            return $serverParams[$offset];
        }
        $lowerOffset = strtolower($offset);
        if (isset($serverParams[$lowerOffset]))
        {
            return $serverParams[$lowerOffset];
        }
    }

    public function __debugInfo(): array
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize()
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
