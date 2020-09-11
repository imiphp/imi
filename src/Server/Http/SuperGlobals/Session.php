<?php

namespace Imi\Server\Http\SuperGlobals;

use Imi\Server\Session\Session as ImiSession;

class Session implements \ArrayAccess, \JsonSerializable
{
    public function offsetSet($offset, $value)
    {
        ImiSession::set($offset, $value);
    }

    public function offsetExists($offset)
    {
        return null !== ImiSession::get($offset);
    }

    public function offsetUnset($offset)
    {
        ImiSession::delete($offset);
    }

    public function offsetGet($offset)
    {
        return ImiSession::get($offset);
    }

    public function __debugInfo()
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize()
    {
        return ImiSession::get();
    }
}
