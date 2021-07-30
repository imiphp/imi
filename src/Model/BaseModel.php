<?php

namespace Imi\Model;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\BeanFactory;
use Imi\Bean\IBean;
use Imi\Bean\ReflectionContainer;
use Imi\Event\IEvent;
use Imi\Event\TEvent;
use Imi\Model\Annotation\Relation\AutoSelect;
use Imi\Model\Event\ModelEvents;
use Imi\Util\Interfaces\IArrayable;
use Imi\Util\LazyArrayObject;
use Imi\Util\ObjectArrayHelper;
use Imi\Util\Text;
use Imi\Util\Traits\TBeanRealClass;

/**
 * 模型基类.
 */
abstract class BaseModel implements \Iterator, \ArrayAccess, IArrayable, \JsonSerializable, IEvent
{
    use TBeanRealClass;
    use TEvent;

    /**
     * 序列化后的所有字段属性名列表.
     *
     * @var array
     */
    protected $__fieldNames;

    /**
     * 驼峰缓存.
     *
     * @var array
     */
    protected static $__camelCache = [];

    /**
     * 方法引用.
     *
     * @var array
     */
    protected static $__methodReference = [];

    /**
     * 元数据集合.
     *
     * @var \Imi\Model\Meta[]
     */
    protected static $__metas;

    /**
     * 当前对象 meta 缓存.
     *
     * @var \Imi\Model\Meta
     */
    protected $__meta;

    /**
     * 真实类名.
     *
     * @var string
     */
    protected $__realClass;

    /**
     * 记录是否存在.
     *
     * @var bool|null
     */
    protected $__recordExists = null;

    /**
     * 序列化字段.
     *
     * @var array|null
     */
    protected $__serializedFields = null;

    /**
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->__meta = $meta = static::__getMeta();
        $this->__fieldNames = $meta->getSerializableFieldNames();
        $this->__realClass = $meta->getClassName();
        if (!$this instanceof IBean)
        {
            $this->__init($data);
        }
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function __init($data = [])
    {
        // 初始化前
        $this->trigger(ModelEvents::BEFORE_INIT, [
            'model' => $this,
            'data'  => $data,
        ], $this, \Imi\Model\Event\Param\InitEventParam::class);

        if ($data)
        {
            $fieldAnnotations = $this->__meta->getFields();
            $dbFieldAnnotations = $this->__meta->getDbFields();
            foreach ($data as $k => $v)
            {
                if (isset($fieldAnnotations[$k]))
                {
                    $fieldAnnotation = $fieldAnnotations[$k];
                }
                elseif (isset($dbFieldAnnotations[$k]))
                {
                    $fieldAnnotation = $dbFieldAnnotations[$k]['column'];
                    $k = $dbFieldAnnotations[$k]['propertyName'];
                }
                else
                {
                    $fieldAnnotation = null;
                }
                if ($fieldAnnotation)
                {
                    switch ($fieldAnnotation->type)
                    {
                        case 'json':
                            $value = json_decode($v, true);
                            if (\is_array($value))
                            {
                                $v = new LazyArrayObject($value);
                            }
                            break;
                        case 'list':
                            if (null !== $v && null !== $fieldAnnotation->listSeparator)
                            {
                                $v = explode($fieldAnnotation->listSeparator, $v);
                            }
                            break;
                    }
                }
                $this[$k] = $v;
            }
        }

        // 初始化后
        $this->trigger(ModelEvents::AFTER_INIT, [
            'model' => $this,
            'data'  => $data,
        ], $this, \Imi\Model\Event\Param\InitEventParam::class);
    }

    /**
     * 实例化当前类.
     *
     * @param mixed ...$args
     *
     * @return static
     */
    public static function newInstance(...$args)
    {
        return BeanFactory::newInstance(static::class, ...$args);
    }

    /**
     * 从记录创建模型对象
     *
     * @param array $data
     *
     * @return static
     */
    public static function createFromRecord(array $data): self
    {
        $model = static::newInstance($data);
        $model->__recordExists = true;

        return $model;
    }

    // 实现接口的方法们：

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        $methodName = 'get' . ucfirst($this->__getCamelName($offset));

        return method_exists($this, $methodName) && null !== $this->$methodName();
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function &offsetGet($offset)
    {
        $methodName = 'get' . ucfirst($this->__getCamelName($offset));
        $realClass = $this->__realClass;
        if (method_exists($this, $methodName))
        {
            $__methodReference = &self::$__methodReference;
            if (!isset($__methodReference[$realClass][$methodName]))
            {
                $refMethod = ReflectionContainer::getMethodReflection(static::class, $methodName);
                $__methodReference[$realClass][$methodName] = $refMethod->returnsReference();
            }
            if ($__methodReference[$realClass][$methodName])
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

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $meta = $this->__meta;
        $fields = $meta->getFields();
        $camelName = $this->__getCamelName($offset);
        // 数据库bit类型字段处理
        if (isset($fields[$offset]))
        {
            $column = $fields[$offset];
        }
        elseif (isset($fields[$camelName]))
        {
            $column = $fields[$camelName];
        }
        else
        {
            $column = null;
        }
        if (null !== $column && 'bit' === $column->type)
        {
            $value = (1 == $value || \chr(1) === $value);
        }

        $methodName = 'set' . ucfirst($camelName);
        if (!method_exists($this, $methodName))
        {
            return;
        }
        $this->$methodName($value);

        if (\is_array($value) || \is_object($value))
        {
            // 提取字段中的属性到当前模型
            $extractProperties = $meta->getExtractPropertys();
            if (
                (($name = $offset) && isset($extractProperties[$name]))
                || (($name = Text::toUnderScoreCase($offset)) && isset($extractProperties[$name]))
                || (($name = $this->__getCamelName($offset)) && isset($extractProperties[$name]))
            ) {
                $this->__parseExtractProperty($name, $extractProperties[$name]);
            }
        }
    }

    /**
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        if (isset($this->__fieldNames[$offset]))
        {
            unset($this->__fieldNames[$offset]);
        }
        else
        {
            $index = array_search($offset, $this->__fieldNames);
            if (false !== $index)
            {
                unset($this->__fieldNames[$index]);
            }
        }
    }

    /**
     * @param mixed $name
     *
     * @return mixed
     */
    public function &__get($name)
    {
        return $this[$name];
    }

    /**
     * @param mixed $name
     * @param mixed $value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this[$name] = $value;
    }

    /**
     * @param mixed $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this[$name]);
    }

    /**
     * @param mixed $name
     *
     * @return void
     */
    public function __unset($name)
    {
        unset($this[$name]);
    }

    /**
     * 将当前对象作为数组返回.
     *
     * @return array
     */
    public function toArray(): array
    {
        $serializedFields = $this->__serializedFields;
        $result = [];
        if (null === $serializedFields)
        {
            $meta = $this->__meta;
            $realClass = $this->__realClass;
            if ($meta->hasRelation())
            {
                $relationFieldNames = ModelRelationManager::getRelationFieldNames($this);
            }
            else
            {
                $relationFieldNames = [];
            }
            // 禁止序列化支持
            $serializables = $meta->getSerializables();
            $serializableSets = $meta->getSerializableSets();
            // JsonNotNull 注解支持
            $propertyJsonNotNullMap = $meta->getPropertyJsonNotNullMap();
            foreach ($this->__fieldNames as $name)
            {
                $value = $this[$name];
                if (null === $value)
                {
                    if (isset($propertyJsonNotNullMap[$name]))
                    {
                        continue;
                    }
                    if (\in_array($name, $relationFieldNames))
                    {
                        /** @var AutoSelect|null $autoSelect */
                        $autoSelect = AnnotationManager::getPropertyAnnotations($realClass, $name, AutoSelect::class)[0] ?? null;
                        if ($autoSelect && !$autoSelect->alwaysShow)
                        {
                            continue;
                        }
                    }
                }
                if (isset($serializableSets[$name]))
                {
                    // 单独属性上的 @Serializable 注解
                    if (!$serializableSets[$name][0]->allow)
                    {
                        continue;
                    }
                }
                elseif ($serializables)
                {
                    if (\in_array($name, $serializables->fields))
                    {
                        // 在黑名单中的字段剔除
                        if ('deny' === $serializables->mode)
                        {
                            continue;
                        }
                    }
                    else
                    {
                        // 不在白名单中的字段剔除
                        if ('allow' === $serializables->mode)
                        {
                            continue;
                        }
                    }
                }
                $result[$name] = $value;
            }

            return $result;
        }
        else
        {
            $resultArray = [];
            $__fieldNames = $this->__fieldNames;
            foreach ($serializedFields as $fieldName)
            {
                if (\in_array($fieldName, $__fieldNames))
                {
                    $name = $fieldName;
                }
                else
                {
                    $name = $__fieldNames[$fieldName] ?? $fieldName;
                }
                $resultArray[$name] = $this[$name] ?? null;
            }

            return $resultArray;
        }
    }

    /**
     * 将当前模型转为数组.
     *
     * 包括属性的值也会被转为数组
     *
     * @param bool $filter 过滤隐藏属性
     *
     * @return array
     */
    public function convertToArray(bool $filter = true): array
    {
        if ($filter)
        {
            $data = $this->toArray();
        }
        else
        {
            $data = iterator_to_array($this);
        }

        $content = json_encode($data);
        if (false === $content)
        {
            return [];
        }

        return json_decode($content, true);
    }

    /**
     * 转换模型数组为模型.
     *
     * @param array $list
     * @param bool  $filter 过滤隐藏属性
     *
     * @return array
     */
    public static function convertListToArray(array $list, bool $filter = true): array
    {
        foreach ($list as &$row)
        {
            /** @var static $row */
            $row = $row->convertToArray($filter);
        }

        return $list;
    }

    /**
     * @return mixed
     */
    public function &current()
    {
        $value = $this[current($this->__fieldNames)];

        return $value;
    }

    /**
     * @return string|bool
     */
    public function key()
    {
        return current($this->__fieldNames);
    }

    /**
     * @return void
     */
    public function next()
    {
        next($this->__fieldNames);
    }

    /**
     * @return void
     */
    public function rewind()
    {
        reset($this->__fieldNames);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return false !== current($this->__fieldNames);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * 从一个数组赋值到当前模型.
     *
     * @param array $data
     *
     * @return void
     */
    public function set(array $data)
    {
        foreach ($data as $k => $v)
        {
            $this[$k] = $v;
        }
    }

    /**
     * 获取驼峰命名.
     *
     * @param string $name
     *
     * @return string
     */
    protected function __getCamelName($name)
    {
        $__camelCache = &self::$__camelCache;
        if (!isset($__camelCache[$name]))
        {
            return $__camelCache[$name] = Text::toCamelName($name);
        }

        return $__camelCache[$name];
    }

    /**
     * 处理导出属性.
     *
     * @param string                                  $propertyName
     * @param \Imi\Model\Annotation\ExtractProperty[] $annotations
     *
     * @return void
     */
    protected function __parseExtractProperty($propertyName, $annotations)
    {
        foreach ($annotations as $annotation)
        {
            if (null === $annotation->alias)
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
     * Get 元数据.
     *
     * @param string|object $object
     *
     * @return \Imi\Model\Meta
     */
    public static function __getMeta($object = null)
    {
        if ($object)
        {
            $class = BeanFactory::getObjectClass($object);
        }
        else
        {
            $class = static::__getRealClassName();
        }
        $__metas = &self::$__metas;
        if (!isset($__metas[$class]))
        {
            return $__metas[$class] = new Meta($class);
        }

        return $__metas[$class];
    }

    /**
     * 获取模型序列化字段.
     *
     * @return array|null
     */
    public function __getSerializedFields(): ?array
    {
        return $this->__serializedFields;
    }

    /**
     * 设置模型序列化字段.
     *
     * @param array|null $serializedFields
     *
     * @return self
     */
    public function __setSerializedFields(?array $serializedFields): self
    {
        $this->__serializedFields = $serializedFields;

        return $this;
    }
}
