<?php

declare(strict_types=1);

namespace Imi\Server\Http\SuperGlobals;

use Imi\Server\Session\Session as ImiSession;

class Session implements \ArrayAccess, \JsonSerializable
{
    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        ImiSession::set($offset, $value);
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return null !== ImiSession::get($offset);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
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

    public function __debugInfo(): array
    {
        return $this->jsonSerialize();
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return ImiSession::get();
    }
}
