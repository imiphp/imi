<?php

declare(strict_types=1);

namespace Imi\Bean;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\Exception\ContainerException;
use Imi\Bean\Parser\BeanParser;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * 单例对象们.
     *
     * @var array
     */
    private $singletonObjects = [];

    /**
     * Bean处理器.
     *
     * @var \Imi\Bean\Parser\BeanParser
     */
    private $beanParser;

    /**
     * 绑定列表.
     *
     * @var array
     */
    private $binds;

    public function __construct($binds = [])
    {
        $this->binds = $binds;
        $this->beanParser = BeanParser::getInstance();
    }

    /**
     * 从容器中获取实例对象，如果不存在则实例化.
     *
     * @param string $id 标识符
     *
     * @throws NotFoundExceptionInterface  没有找到对象
     * @throws ContainerExceptionInterface 检索时出错
     *
     * @return mixed Entry.
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
            throw new ContainerException('id is not a string value');
        }
        if ('' === $id)
        {
            throw new ContainerException('id can not be a empty string value');
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
            $data = $this->beanParser->getData();
            if (isset($data[$id]))
            {
                $object = BeanFactory::newInstanceNoInit($data[$id]['className'], ...$params);
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
            if ([] === $params && (!isset($data[$id]['instanceType']) || Bean::INSTANCE_TYPE_SINGLETON === $data[$id]['instanceType']))
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
     *
     * @param string $name
     * @param string $class
     *
     * @return void
     */
    public function bind($name, $class)
    {
        $this->binds[$name] = $class;
    }

    /**
     * Get 绑定列表.
     *
     * @return array
     */
    public function getBinds()
    {
        return $this->binds;
    }

    /**
     * 返回一个新的容器对象，容器内无对象，仅有列表关联.
     *
     * @return static
     */
    public function newSubContainer()
    {
        return new static($this->binds);
    }
}
