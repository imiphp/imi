<?php

namespace Imi\Server\Http\SuperGlobals;

use Imi\RequestContext;

class Files implements \ArrayAccess, \JsonSerializable
{
    public function offsetSet($offset, $value)
    {
        trigger_error('imi does not support to assign values to $_FILES', \E_USER_WARNING);
    }

    public function offsetExists($offset)
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');
        $files = $request->getUploadedFiles($offset);

        return isset($files[$offset]);
    }

    public function offsetUnset($offset)
    {
        trigger_error('imi does not support to unset values from $_FILES', \E_USER_WARNING);
    }

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

    public function __debugInfo()
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize()
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');
        $files = $request->getUploadedFiles();
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
