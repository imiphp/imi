<?php
namespace Imi\Model;

use Imi\Util\Text;
use Imi\Event\IEvent;
use Imi\Event\TEvent;
use Imi\Bean\BeanFactory;
use Imi\Util\ClassObject;
use Imi\Util\LazyArrayObject;
use Imi\Util\ObjectArrayHelper;
use Imi\Model\Annotation\Column;
use Imi\Model\Event\ModelEvents;
use Imi\Util\Interfaces\IArrayable;
use Imi\Model\Annotation\Serializable;
use Imi\Model\Event\Param\InitEventParam;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Model\Annotation\Relation\AutoSelect;
use Imi\Bean\IBean;

/**
 * 模型基类
 */
abstract class BaseModel implements \Iterator, \ArrayAccess, IArrayable, \JsonSerializable, IEvent
{
    use TEvent;

    /**
     * 数据库原始字段名称
     * @var array
     */
    protected $__fieldNames;

    /**
     * 驼峰缓存
     * @var array
     */
    protected $__camelCache = [];

    /**
     * 从存储中读取出来的原始数据
     *
     * @var array
     */
    protected $__originValues = [];

    public function __construct($data = [])
    {
        if(!$this instanceof IBean)
        {
            $this->__init();
        }
    }

    public function __init($data = [])
    {
        $this->__originValues = $data;
        $this->__fieldNames = array_merge(ModelManager::getFieldNames($this), ModelRelationManager::getRelationFieldNames($this));

        $data = new LazyArrayObject($data);

        // 初始化前
        $this->trigger(ModelEvents::BEFORE_INIT, [
            'model' => $this,
            'data'  => $data,
        ], $this, \Imi\Model\Event\Param\InitEventParam::class);

        $fieldAnnotations = ModelManager::getFields($this);
        foreach($data as $k => $v)
        {
            if(isset($fieldAnnotations[$k]))
            {
                switch($fieldAnnotations[$k]->type)
                {
                    case 'json':
                        $value = json_decode($v, true);
                        if(is_array($value))
                        {
                            $v = new LazyArrayObject($value);
                        }
                        break;
                }
            }
            $this[$k] = $v;
        }

        // 初始化后
        $this->trigger(ModelEvents::AFTER_INIT, [
            'model' => $this,
            'data'  => $data,
        ], $this, \Imi\Model\Event\Param\InitEventParam::class);
    }
    
    /**
     * 实例化当前类
     * @param mixed ...$args
     * @return static
     */
    public static function newInstance(...$args)
    {
        return BeanFactory::newInstance(static::class, ...$args);
    }

    // 实现接口的方法们：

    public function offsetExists($offset)
    {
        $methodName = 'get' . ucfirst($this->__getCamelName($offset));
        return method_exists($this, $methodName) && null !== call_user_func([$this, $methodName]);
    }

    public function &offsetGet($offset)
    {
        $methodName = 'get' . ucfirst($this->__getCamelName($offset));
        if(method_exists($this, $methodName))
        {
            $result = call_user_func([$this, $methodName]);
        }
        else
        {
            $result = null;
        }
        return $result;
    }

    public function offsetSet($offset, $value)
    {
        // 数据库bit类型字段处理
        $column = ModelManager::getPropertyAnnotation($this, $offset, Column::class);
        if(null === $column)
        {
            $column = ModelManager::getPropertyAnnotation($this, $this->__getCamelName($offset), Column::class);
        }
        if(null !== $column)
        {
            if('bit' === $column->type)
            {
                $value = (1 == $value || chr(1) == $value);
            }
        }

        $methodName = 'set' . ucfirst($this->__getCamelName($offset));
        if(!method_exists($this, $methodName))
        {
            return;
        }
        call_user_func([$this, $methodName], $value);

        if(is_array($value) || is_object($value))
        {
            // 提取字段中的属性到当前模型
            $extractProperties = ModelManager::getExtractPropertys($this);
            if(
                (($name = $offset) && isset($extractProperties[$name]))
                || (($name = Text::toUnderScoreCase($offset)) && isset($extractProperties[$name]))
                || (($name = Text::toCamelName($offset)) && isset($extractProperties[$name]))
            )
            {
                $this->parseExtractProperty($name, $extractProperties[$name]);
            }
        }

    }

    public function offsetUnset($offset)
    {
        $index = array_search($offset, $this->__fieldNames);
        if(false !== $index)
        {
            unset($this->__fieldNames[$index]);
        }
    }

    public function &__get($name)
    {
        return $this[$name];
    }

    public function __set($name, $value)
    {
        $this[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this[$name]);
    }

    public function __unset($name)
    {
        unset($this[$name]);
    }

    /**
     * 将当前对象作为数组返回
     * @return array
     */
    public function toArray(): array
    {
        $result = \iterator_to_array($this);
        $className = BeanFactory::getObjectClass($this);
        // 支持注解配置隐藏为null的关联属性
        foreach(ModelRelationManager::getRelationFieldNames($this) as $name)
        {
            if(array_key_exists($name, $result) && null === $result[$name])
            {
                $autoSelect = AnnotationManager::getPropertyAnnotations($className, $name, AutoSelect::class)[0] ?? null;
                if($autoSelect && !$autoSelect->alwaysShow)
                {
                    unset($result[$name]);
                }
            }
        }
        // 禁止序列化支持
        $serializables = ModelManager::getSerializables($this);
        $serializableSets = AnnotationManager::getPropertiesAnnotations($className, Serializable::class);
        foreach($result as $propertyName => $value)
        {
            if(isset($serializableSets[$propertyName]))
            {
                // 单独属性上的 @Serializable 注解
                if(!$serializableSets[$propertyName][0]->allow)
                {
                    unset($result[$propertyName]);
                }
            }
            else if($serializables)
            {
                if(in_array($propertyName, $serializables->fields))
                {
                    // 在黑名单中的字段剔除
                    if('deny' === $serializables->mode)
                    {
                        unset($result[$propertyName]);
                    }
                }
                else
                {
                    // 不在白名单中的字段剔除
                    if('allow' === $serializables->mode)
                    {
                        unset($result[$propertyName]);
                    }
                }
            }
        }
        return $result;
    }
    
    public function &current()
    {
        $value = $this[$this->__getFieldName(current($this->__fieldNames))];
        return $value;
    }

    public function key()
    {
        return $this->__getFieldName(current($this->__fieldNames));
    }

    public function next()
    {
        next($this->__fieldNames);
    }

    public function rewind()
    {
        reset($this->__fieldNames);
    }

    public function valid()
    {
        return false !== $this->__getFieldName(current($this->__fieldNames));
    }
    
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * 从一个数组赋值到当前模型
     *
     * @param array $data
     * @return void
     */
    public function set(array $data)
    {
        foreach($data as $k => $v)
        {
            $this[$k] = $v;
        }
    }

    /**
     * 获取驼峰命名
     * @param string $name
     * @return string
     */
    protected function __getCamelName($name)
    {
        if(!isset($this->__camelCache[$name]))
        {
            $this->__camelCache[$name] = Text::toCamelName($name);
        }
        return $this->__camelCache[$name];
    }

    protected function __getFieldName($fieldName)
    {
        if(false === $fieldName)
        {
            return false;
        }
        if(ModelManager::isCamel($this))
        {
            return $this->__getCamelName($fieldName);
        }
        else
        {
            return $fieldName;
        }
    }

    protected function parseExtractProperty($propertyName, $annotations)
    {
        foreach($annotations as $annotation)
        {
            if(null === $annotation->alias)
            {
                $list = explode('.', $annotation->fieldName);
                $setPropertyName = end($list);
            }
            else
            {
                $setPropertyName = $annotation->alias;
            }
            $this[$setPropertyName] = $value = ObjectArrayHelper::get($this[$propertyName], $annotation->fieldName);
        }
    }
}