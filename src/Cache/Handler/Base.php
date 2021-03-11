<?php

namespace Imi\Cache\Handler;

use Imi\App;
use Imi\Cache\InvalidArgumentException;
use Imi\RequestContext;
use Psr\SimpleCache\CacheInterface;

abstract class Base implements CacheInterface
{
    /**
     * 数据读写格式化处理器
     * 为null时不做任何处理.
     *
     * @var string
     */
    protected $formatHandlerClass;

    /**
     * @param array $option
     */
    public function __construct($option = [])
    {
        foreach ($option as $k => $v)
        {
            $this->$k = $v;
        }
    }

    /**
     * 写入编码
     *
     * @param mixed $data
     *
     * @return mixed
     */
    protected function encode($data)
    {
        if (null === $this->formatHandlerClass)
        {
            return $data;
        }
        elseif (null !== RequestContext::getServer())
        {
            return RequestContext::getServerBean($this->formatHandlerClass)->encode($data);
        }
        else
        {
            return App::getBean($this->formatHandlerClass)->encode($data);
        }
    }

    /**
     * 读出解码
     *
     * @param mixed $data
     *
     * @return mixed
     */
    protected function decode($data)
    {
        if (null === $this->formatHandlerClass)
        {
            return $data;
        }
        elseif (null !== RequestContext::getServer())
        {
            return RequestContext::getServerBean($this->formatHandlerClass)->decode($data);
        }
        else
        {
            return App::getBean($this->formatHandlerClass)->decode($data);
        }
    }

    /**
     * 检查key格式.
     *
     * @param string $key
     *
     * @return void
     */
    protected function checkKey($key)
    {
        if (!\is_string($key))
        {
            throw new InvalidArgumentException('Key must be a string');
        }
    }

    /**
     * 检查值是否是数组或Traversable.
     *
     * @param mixed $values
     *
     * @return void
     */
    protected function checkArrayOrTraversable($values)
    {
        if (!\is_array($values) && !$values instanceof \Traversable)
        {
            throw new InvalidArgumentException('Invalid keys');
        }
    }
}
