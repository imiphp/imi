<?php

declare(strict_types=1);

namespace Imi\Server\Http\SuperGlobals;

use Imi\Log\Log;
use Imi\RequestContext;

class Files implements \ArrayAccess, \JsonSerializable
{
    public function offsetSet(mixed $key, mixed $value): void
    {
        Log::warning('imi does not support to assign values to $_FILES');
    }

    public function offsetExists(mixed $key): bool
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');
        $files = $request->getUploadedFiles();

        return isset($files[$key]);
    }

    public function offsetUnset(mixed $key): void
    {
        Log::warning('imi does not support to unset values from $_FILES');
    }

    public function offsetGet(mixed $key): mixed
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');
        $files = $request->getUploadedFiles();
        if (isset($files[$key]))
        {
            return [
                'name'      => $files[$key]->getClientFilename(),
                'type'      => $files[$key]->getClientMediaType(),
                'tmp_name'  => $files[$key]->getStream()->getMetadata('uri'),
                'error'     => $files[$key]->getError(),
                'size'      => $files[$key]->getSize(),
            ];
        }

        return null;
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
