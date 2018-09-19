<?php
namespace Imi\Bean;

use Imi\Bean\Proxy\Proxy;
use Imi\Bean\Annotation\Bean;
use Imi\Bean\Parser\BeanParser;
use Psr\Container\ContainerInterface;
use Imi\Bean\Exception\ContainerException;

class Container implements ContainerInterface
{
    /**
     * 单例对象们
     * @var array
     */
    public $singletonObjects = [];

    /**
     * Bean处理器
     * @var \Imi\Bean\Parser\BeanParser
     */
    public $beanParser;

    public function __construct()
    {
        $this->beanParser = BeanParser::getInstance();
    }

    /**
     * 从容器中获取实例对象，如果不存在则实例化
     * @param string $id 标识符
     * @throws NotFoundExceptionInterface  没有找到对象
     * @throws ContainerExceptionInterface 检索时出错
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if(!is_string($id))
        {
            throw new ContainerException('id is not a string value');
        }
        if('' === $id)
        {
            throw new ContainerException('id can not be a empty string value');
        }
        
        // 实现传递实例化参数
        $params = func_get_args();
        array_shift($params);
        // 单例中有数据，且无实例化参数时直接返回单例
        if(isset($this->singletonObjects[$id]) && !isset($params[0]))
        {
            return $this->singletonObjects[$id];
        }
        $data = $this->beanParser->getData();
        
        if(isset($data[$id]))
        {
            $object = BeanFactory::newInstance($data[$id]['className'], ...$params);
        }
        else if(class_exists($id))
        {
            $object = BeanFactory::newInstance($id, ...$params);
        }
        else
        {
            throw new ContainerException(sprintf('%s not found', $id));
        }

        // 传参实例化强制不使用单例
        if([] === $params && isset($data[$id]['instanceType']) && $data[$id]['instanceType'] === Bean::INSTANCE_TYPE_SINGLETON)
        {
            $this->singletonObjects[$id] = $object;
        }
        return $object;
    }

    /**
     * 实例对象是否存在
     * @param string $id 标识符
     * @return bool
     */
    public function has($id)
    {
        return is_string($id) && '' !== $id && isset($this->singletonObjects[$id]);
    }

}