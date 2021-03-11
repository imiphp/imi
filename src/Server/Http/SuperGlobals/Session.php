<?php

namespace Imi\Server\Http\SuperGlobals;

use Imi\Server\Session\Session as ImiSession;

class Session implements \ArrayAccess, \JsonSerializable
{
    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        ImiSession::set($offset, $value);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return null !== ImiSession::get($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        ImiSession::delete($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return ImiSession::get($offset);
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        return $this->jsonSerialize();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return ImiSession::get();
    }
}
