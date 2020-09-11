<?php

namespace Imi\Server\Http\SuperGlobals;

use Imi\RequestContext;

class Post implements \ArrayAccess, \JsonSerializable
{
    public function offsetSet($offset, $value)
    {
        trigger_error('imi does not support to assign values to $_POST', \E_USER_WARNING);
    }

    public function offsetExists($offset)
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');

        return null !== $request->post($offset);
    }

    public function offsetUnset($offset)
    {
        trigger_error('imi does not support to unset values from $_POST', \E_USER_WARNING);
    }

    public function offsetGet($offset)
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');

        return $request->post($offset);
    }

    public function __debugInfo()
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize()
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');

        return $request->post();
    }
}
