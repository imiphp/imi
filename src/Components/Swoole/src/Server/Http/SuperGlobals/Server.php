<?php

declare(strict_types=1);

namespace Imi\Swoole\Server\Http\SuperGlobals;

use Imi\RequestContext;

class Server implements \ArrayAccess, \JsonSerializable
{
    /**
     * 默认的 $_SERVER 变量.
     *
     * @var array
     */
    private array $defaultServer = [];

    public function __construct($defaultServer)
    {
        $this->defaultServer = $defaultServer;
    }

    public function offsetSet($offset, $value)
    {
        $this->defaultServer[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        if (isset($this->defaultServer[$offset]))
        {
            return true;
        }
        /** @var \Imi\Server\Http\Message\Request $request */
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

    public function offsetUnset($offset)
    {
        trigger_error('imi does not support to unset values from $_SERVER', \E_USER_WARNING);
    }

    public function offsetGet($offset)
    {
        /** @var \Imi\Server\Http\Message\Request $request */
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

    public function __debugInfo()
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize()
    {
        /** @var \Imi\Server\Http\Message\Request $request */
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
