<?php

declare(strict_types=1);

namespace Imi\Server\Http\SuperGlobals;

use Imi\Log\Log;
use Imi\RequestContext;

class Files implements \ArrayAccess, \JsonSerializable
{
    /**
     * @param int|string $offset
     * @param mixed      $value
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value): void
    {
        Log::warning('imi does not support to assign values to $_FILES');
    }

    /**
     * @param int|string $offset
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset): bool
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');
        $files = $request->getUploadedFiles();

        return isset($files[$offset]);
    }

    /**
     * @param int|string $offset
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset): void
    {
        Log::warning('imi does not support to unset values from $_FILES');
    }

    /**
     * @param int|string $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');
        $files = $request->getUploadedFiles();
        if (isset($files[$offset]))
        {
            return [
                'name'      => $files[$offset]->getClientFilename(),
                'type'      => $files[$offset]->getClientMediaType(),
                'tmp_name'  => $files[$offset]->getStream()->getMetadata('uri'),
                'error'     => $files[$offset]->getError(),
                'size'      => $files[$offset]->getSize(),
            ];
        }
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
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');
        $files = $request->getUploadedFiles();
        if (!$files)
        {
            return [];
        }
        $result = [];
        foreach ($files as $key => $file)
        {
            $result[$key] = [
                'name'      => $file->getClientFilename(),
                'type'      => $file->getClientMediaType(),
                'tmp_name'  => $file->getStream()->getMetadata('uri'),
                'error'     => $file->getError(),
                'size'      => $file->getSize(),
            ];
        }

        return $result;
    }
}
