<?php
namespace Imi\Model;

use Imi\Util\Text;
use Imi\Bean\IBean;
use Imi\Event\IEvent;
use Imi\Event\TEvent;
use Imi\Bean\BeanFactory;
use Imi\Util\ClassObject;
use Imi\Util\LazyArrayObject;
use Imi\Util\ObjectArrayHelper;
use Imi\Model\Annotation\Column;
use Imi\Model\Event\ModelEvents;
use Imi\Util\Interfaces\IArrayable;
use Imi\Util\Traits\TBeanRealClass;
use Imi\Model\Annotation\Serializable;
use Imi\Model\Event\Param\InitEventParam;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Model\Annotation\Relation\AutoSelect;

/**
 * 模型基类
 */
abstract class BaseModel implements \Iterator, \ArrayAccess, IArrayable, \JsonSerializable, IEvent
{
    use TEvent, TBeanRealClass;

    /**
     * 数据库原始字段名称
     * @var array
     */
    protected $__fieldNames;

    /**
     * 驼峰缓存
     * @var array
     */
    protected static $__camelCache = [];

    /**
     * 方法引用
     *
     * @var array
     */
    protected static $__methodReference = [];

    /**
     * 元数据集合
     *
     * @var \Imi\Model\Meta[]
     */
    protected static $__metas;

    /**
     * 当前对象 meta 缓存
     *
     * @var \Imi\Model\Meta
     */
    protected $__meta;

    /**
     * 真实类名
     *
     * @var string
     */
    protected $__realClass;

    public function __construct($data = [])
    {
        $this->__meta = $meta = static::__getMeta();
        $this->__fieldNames = $meta->getRealFieldNames();
        $this->__realClass = $meta->getClassName();
        if(!$this instanceof IBean)
        {
            $this->__init($data);
        }
    }

    public function __init($data = [])
    {
        // 初始化前
        $this->trigger(ModelEvents::BEFORE_INIT, [
            'model' => $this,
            'data'  => $data,
        ], $this, \Imi\Model\Event\Param\InitEventParam::class);

        $fieldAnnotations = $this->__meta->getFields();
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
        return method_exists($this, $methodName) && null !== $this->$methodName();
    }

    public function &offsetGet($offset)
    {
        $methodName = 'get' . ucfirst($this->__getCamelName($offset));
        $realClass = $this->__realClass;
        if(method_exists($this, $methodName))
        {
            if(!isset(self::$__methodReference[$realClass][$methodName]))
            {
                $refMethod = new \ReflectionMethod($this, $methodName);
                self::$__methodReference[$realClass][$methodName] = $refMethod->returnsReference();
            }
            if(self::$__methodReference[$realClass][$methodName])
            {
                return $this->$methodName();
            }
            else
            {
                $result = $this->$methodName();
            }
        }
        else
        {
            $result = null;
        }
        return $result;
    }

    public function offsetSet($offset, $value)
    {
        $meta = $this->__meta;
        $fields = $meta->getFields();
        $camelName = $this->__getCamelName($offset);
        // 数据库bit类型字段处理
        $column = null;
        if(isset($fields[$offset]))
        {
            $column = $fields[$offset];
        }
        else
        {
            if(isset($fields[$camelName]))
            {
                $column = $fields[$camelName];
            }
        }
        if(null !== $column && 'bit' === $column->type)
        {
            $value = (1 == $value || chr(1) === $value);
        }

        $methodName = 'set' . ucfirst($camelName);
        if(!method_exists($this, $methodName))
        {
            return;
        }
        $this->$methodName($value);

        if(is_array($value) || is_object($value))
        {
            // 提取字段中的属性到当前模型
            $extractProperties = $meta->getExtractPropertys();
            if(
                (($name = $offset) && isset($extractProperties[$name]))
                || (($name = Text::toUnderScoreCase($offset)) && isset($extractProperties[$name]))
                || (($name = $this->__getCamelName($offset)) && isset($extractProperties[$name]))
            )
            {
                $this->__parseExtractProperty($name, $extractProperties[$name]);
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
        $meta = $this->__meta;
        $realClass = $this->__realClass;
        if($meta->hasRelation())
        {
            // 支持注解配置隐藏为null的关联属性
            foreach(ModelRelationManager::getRelationFieldNames($this) as $name)
            {
                if(array_key_exists($name, $result) && null === $result[$name])
                {
                    $autoSelect = AnnotationManager::getPropertyAnnotations($realClass, $name, AutoSelect::class)[0] ?? null;
                    if($autoSelect && !$autoSelect->alwaysShow)
                    {
                        unset($result[$name]);
                    }
                }
            }
        }
        // 禁止序列化支持
        $serializables = $meta->getSerializables();
        $serializableSets = $meta->getSerializableSets();
        if($serializables || $serializableSets)
        {
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
        if(!isset(self::$__camelCache[$name]))
        {
            self::$__camelCache[$name] = Text::toCamelName($name);
        }
        return self::$__camelCache[$name];
    }

    /**
     * 获取字段名
     *
     * @param string $fieldName
     * @return void
     */
    protected function __getFieldName($fieldName)
    {
        if(false === $fieldName)
        {
            return false;
        }
        if($this->__meta->isCamel())
        {
            return $this->__getCamelName($fieldName);
        }
        else
        {
            return $fieldName;
        }
    }

    /**
     * 处理导出属性
     *
     * @param string $propertyName
     * @param \Imi\Model\Annotation\ExtractProperty[] $annotations
     * @return void
     */
    protected function __parseExtractProperty($propertyName, $annotations)
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
            $this[$setPropertyName] = ObjectArrayHelper::get($this[$propertyName], $annotation->fieldName);
        }
    }

    /**
     * Get 元数据
     *
     * @param string|object $object
     * @return \Imi\Model\Meta
     */ 
    public static function __getMeta($object = null)
    {
        if($object)
        {
            $class = BeanFactory::getObjectClass($object);
        }
        else
        {
            $class = static::__getRealClassName();
        }
        if(!isset(self::$__metas[$class]))
        {
            self::$__metas[$class] = new Meta($class);
        }
        return self::$__metas[$class];
    }

}