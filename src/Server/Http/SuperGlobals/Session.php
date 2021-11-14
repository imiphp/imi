<?php

declare(strict_types=1);

namespace Imi\Server\Http\SuperGlobals;

use Imi\Server\Session\Session as ImiSession;

class Session implements \ArrayAccess, \JsonSerializable
{
    /**
     * @param int|string $offset
     * @param mixed      $value
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value): void
    {
        ImiSession::set($offset, $value);
    }

    /**
     * @param int|string $offset
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset): bool
    {
        return null !== ImiSession::get($offset);
    }

    /**
     * @param int|string $offset
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset): void
    {
        ImiSession::delete($offset);
    }

    /**
     * @param int|string $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return ImiSession::get($offset);
    }

    public function __debugInfo(): array
    {
        return $this->jsonSerialize();
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return ImiSession::get();
    }
}
