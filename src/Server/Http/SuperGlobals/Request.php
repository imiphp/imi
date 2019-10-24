<?php
namespace Imi\Server\Http\SuperGlobals;

use Imi\RequestContext;

class Request implements \ArrayAccess, \JsonSerializable
{
    public function offsetSet($offset, $value)
    {
        user_error('imi does not support to assign values to $_REQUEST', E_USER_WARNING);
    }

    public function offsetExists($offset)
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');
        return null !== $request->request($offset);
    }

    public function offsetUnset($offset)
    {
        user_error('imi does not support to unset values from $_REQUEST', E_USER_WARNING);
    }

    public function offsetGet($offset)
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');
        return $request->request($offset);
    }

    public function __debugInfo()
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize()
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');
        return $request->request();
    }

}
