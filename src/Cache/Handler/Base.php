<?php

declare(strict_types=1);

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
     */
    protected ?string $formatHandlerClass = null;

    public function __construct(array $option = [])
    {
        if ($option)
        {
            foreach ($option as $k => $v)
            {
                $this->$k = $v;
            }
        }
    }

    /**
     * 写入编码
     *
     * @param mixed $data
     */
    protected function encode($data): string
    {
        if (null === $this->formatHandlerClass)
        {
            return $data;
        }
        elseif (null !== ($server = RequestContext::getServer()))
        {
            return $server->getBean($this->formatHandlerClass)->encode($data);
        }
        else
        {
            return App::getBean($this->formatHandlerClass)->encode($data);
        }
    }

    /**
     * 读出解码
     *
     * @return mixed
     */
    protected function decode(string $data)
    {
        if (null === $this->formatHandlerClass)
        {
            return $data;
        }
        elseif (null !== ($server = RequestContext::getServer()))
        {
            return $server->getBean($this->formatHandlerClass)->decode($data);
        }
        else
        {
            return App::getBean($this->formatHandlerClass)->decode($data);
        }
    }

    /**
     * 检查key格式.
     */
    protected function checkKey(string $key): void
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
     */
    protected function checkArrayOrTraversable($values): void
    {
        if (!\is_array($values) && !$values instanceof \Traversable)
        {
            throw new InvalidArgumentException('Invalid keys');
        }
    }
}
