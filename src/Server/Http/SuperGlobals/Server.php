<?php
namespace Imi\Server\Http\SuperGlobals;

use Imi\RequestContext;
use Imi\Exception\RequestContextException;

class Server implements \ArrayAccess, \JsonSerializable
{
    /**
     * 默认的 $_SERVER 变量
     *
     * @var array
     */
    private $defaultServer;

    public function __construct($defaultServer)
    {
        $this->defaultServer = $defaultServer;
    }

    public function offsetSet($offset, $value)
    {
        $this->defaultServer[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        if(isset($this->defaultServer[$offset]))
        {
            return true;
        }
        try {
            /** @var \Imi\Server\Http\Message\Request $request */
            $request = RequestContext::get('request');
            $serverParams = $request->getServerParams();
            if(isset($serverParams[$offset]) || isset($serverParams[strtolower($offset)]))
            {
                return true;
            }
        } catch(RequestContextException $e) {
            
        }
        return false;
    }

    public function offsetUnset($offset)
    {
        user_error('imi does not support to unset values from $_SERVER', E_USER_WARNING);
    }

    public function offsetGet($offset)
    {
        try {
            /** @var \Imi\Server\Http\Message\Request $request */
            $request = RequestContext::get('request');
            $serverParams = $request->getServerParams();
            if(isset($serverParams[$offset]))
            {
                return $serverParams[$offset];
            }
            $lowerOffset = strtolower($offset);
            if(isset($serverParams[$lowerOffset]))
            {
                return $serverParams[$lowerOffset];
            }
        } catch(RequestContextException $e) {

        } finally {
            if(isset($this->defaultServer[$offset]))
            {
                return $this->defaultServer[$offset];
            }
        }
    }

    public function __debugInfo()
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize()
    {
        try {
            /** @var \Imi\Server\Http\Message\Request $request */
            $request = RequestContext::get('request');
            $serverParams = $request->getServerParams();
            return array_merge($this->defaultServer, array_change_key_case($serverParams, CASE_UPPER));
        } catch(RequestContextException $e) {
            return $this->defaultServer;
        } finally {
            
        }
    }

}
