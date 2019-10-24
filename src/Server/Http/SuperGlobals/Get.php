<?php
namespace Imi\Server\Http\SuperGlobals;

use Imi\RequestContext;

class Get implements \ArrayAccess, \JsonSerializable
{
    public function offsetSet($offset, $value)
    {
        user_error('imi does not support to assign values to $_GET', E_USER_WARNING);
    }

    public function offsetExists($offset)
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');
        return null !== $request->get($offset);
    }

    public function offsetUnset($offset)
    {
        user_error('imi does not support to unset values from $_GET', E_USER_WARNING);
    }

    public function offsetGet($offset)
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');
        return $request->get($offset);
    }

    public function __debugInfo()
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize()
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');
        return $request->get();
    }

}
