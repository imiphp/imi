<?php

declare(strict_types=1);

namespace Imi\Server\Http\SuperGlobals;

use Imi\Server\Session\Session as ImiSession;

class Session implements \ArrayAccess, \JsonSerializable
{
    public function offsetSet(mixed $key, mixed $value): void
    {
        ImiSession::set($key, $value);
    }

    public function offsetExists(mixed $key): bool
    {
        return null !== ImiSession::get($key);
    }

    public function offsetUnset(mixed $key): void
    {
        ImiSession::delete($key);
    }

    public function offsetGet(mixed $key): mixed
    {
        return ImiSession::get($key);
    }

    public function __debugInfo(): array
    {
        return $this->jsonSerialize();
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return ImiSession::get();
    }
}
