<?php

declare(strict_types=1);

namespace Imi\Bean;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\Exception\ContainerException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * 单例对象们.
     */
    private array $singletonObjects = [];

    /**
     * 绑定列表.
     */
    private array $binds = [];

    public function __construct(array $binds = [])
    {
        $this->binds = $binds;
    }

    /**
     * 从容器中获取实例对象，如果不存在则实例化.
     *
     * @param string $id 标识符
     *
     * @throws NotFoundExceptionInterface  没有找到对象
     * @throws ContainerExceptionInterface 检索时出错
     *
     * @return mixed entry
     */
    public function get($id)
    {
        // 实现传递实例化参数
        $params = \func_get_args();
        // 单例中有数据，且无实例化参数时直接返回单例
        $singletonObjects = &$this->singletonObjects;
        if (isset($singletonObjects[$id]) && 1 === \func_num_args())
        {
            return $singletonObjects[$id];
        }

        if (!\is_string($id))
        {
            throw new ContainerException('$id is not a string value');
        }
        if ('' === $id)
        {
            throw new ContainerException('$id can not be a empty string value');
        }

        unset($params[0]);

        $binds = &$this->binds;
        if (isset($binds[$id]))
        {
            $object = BeanFactory::newInstanceNoInit($binds[$id], ...$params);
            if ([] === $params)
            {
                $singletonObjects[$id] = $object;
            }
        }
        else
        {
            $data = BeanManager::get($id);
            if ($data)
            {
                $object = BeanFactory::newInstanceNoInit($data['className'], ...$params);
            }
            elseif (class_exists($id))
            {
                $object = BeanFactory::newInstanceNoInit($id, ...$params);
            }
            else
            {
                throw new ContainerException(sprintf('%s not found', $id));
            }

            // 传参实例化强制不使用单例
            if ([] === $params && (!isset($data['instanceType']) || Bean::INSTANCE_TYPE_SINGLETON === $data['instanceType']))
            {
                $singletonObjects[$id] = $object;
            }
        }

        BeanFactory::initInstance($object, $params);

        return $object;
    }

    /**
     * 实例对象是否存在.
     *
     * @param string $id 标识符
     *
     * @return bool
     */
    public function has($id)
    {
        return '' !== $id && isset($this->singletonObjects[$id]);
    }

    /**
     * 绑定名称和类名.
     */
    public function bind(string $name, string $class): void
    {
        $this->binds[$name] = $class;
    }

    /**
     * Get 绑定列表.
     */
    public function getBinds(): array
    {
        return $this->binds;
    }

    /**
     * 返回一个新的容器对象，容器内无对象，仅有列表关联.
     *
     * @return static
     */
    public function newSubContainer(): self
    {
        return new static($this->binds);
    }
}
