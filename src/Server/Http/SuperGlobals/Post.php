<?php

declare(strict_types=1);

namespace Imi\Server\Http\SuperGlobals;

use Imi\RequestContext;

class Post implements \ArrayAccess, \JsonSerializable
{
    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        trigger_error('imi does not support to assign values to $_POST', \E_USER_WARNING);
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');

        return null !== $request->post($offset);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        trigger_error('imi does not support to unset values from $_POST', \E_USER_WARNING);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');

        return $request->post($offset);
    }

    public function __debugInfo(): array
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
