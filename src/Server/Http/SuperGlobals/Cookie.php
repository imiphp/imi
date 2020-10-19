<?php

namespace Imi\Server\Http\SuperGlobals;

use Imi\RequestContext;

class Cookie implements \ArrayAccess, \JsonSerializable
{
    public function offsetSet($offset, $value)
    {
        trigger_error('imi does not support to assign values to $_COOKIE', \E_USER_WARNING);
    }

    public function offsetExists($offset)
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');

        return null !== $request->getCookie($offset);
    }

    public function offsetUnset($offset)
    {
        trigger_error('imi does not support to unset values from $_COOKIE', \E_USER_WARNING);
    }

    public function offsetGet($offset)
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');

        return $request->getCookie($offset);
    }

    public function __debugInfo()
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize()
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');

        return $request->getCookieParams();
    }
}
