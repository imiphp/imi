<?php

namespace Imi\Server\Http\SuperGlobals;

use Imi\RequestContext;

class Files implements \ArrayAccess, \JsonSerializable
{
    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        trigger_error('imi does not support to assign values to $_FILES', \E_USER_WARNING);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        /** @var \Imi\Server\Http\Message\Request $request */
        $request = RequestContext::get('request');
        $files = $request->getUploadedFiles();

        return isset($files[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        trigger_error('imi does not support to unset values from $_FILES', \E_USER_WARNING);
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
