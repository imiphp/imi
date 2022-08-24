<?php

declare(strict_types=1);

namespace Imi\Cache\Handler;

use Imi\Bean\Annotation\Bean;
use Imi\RequestContext as ImiRequestContext;
use Imi\Util\DateTime;
use Imi\Util\ExpiredStorage;

/**
 * @Bean("RequestContextCache")
 */
class RequestContext extends Base
{
    /**
     * 在请求上下文中的键名.
     */
    protected string $key = 'RequestContextCache';

    /**
     * {@inheritDoc}
     */
    public function get($key, $default = null)
    {
        return $this->getObject()->get($key, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value, $ttl = null)
    {
        // ttl 支持 \DateInterval 格式
        if ($ttl instanceof \DateInterval)
        {
            $ttl = DateTime::getSecondsByInterval($ttl);
        }
        $this->getObject()->set($key, $value, (int) $ttl);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function delete($key)
    {
        $this->getObject()->unset($key);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->getObject()->clear();

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getMultiple($keys, $default = null)
    {
        $this->checkArrayOrTraversable($keys);
        $object = $this->getObject();
        $result = [];
        foreach ($keys as $key)
        {
            $result[$key] = $object->get($key, $default);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        $this->checkArrayOrTraversable($values);
        // ttl 支持 \DateInterval 格式
        if ($ttl instanceof \DateInterval)
        {
            $ttl = DateTime::getSecondsByInterval($ttl);
        }
        $object = $this->getObject();
        foreach ($values as $key => $value)
        {
            $object->set($key, $value, $ttl ?? 0);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteMultiple($keys)
    {
        $this->checkArrayOrTraversable($keys);
        $object = $this->getObject();
        foreach ($keys as $key)
        {
            $object->unset($key);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function has($key)
    {
        return $this->getObject()->isset($key);
    }

    protected function getObject(): ExpiredStorage
    {
        return ImiRequestContext::getContext()[$this->key] ??= new ExpiredStorage();
    }
}
