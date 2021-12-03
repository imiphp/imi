<?php

declare(strict_types=1);

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
     * 驼峰缓存.
     */
    protected static array $__camelCache = [];

    /**
     * 方法引用.
     */
    protected static array $__methodReference = [];

    /**
     * 元数据集合.
     *
     * @var \Imi\Model\Meta[]
     */
    protected static array $__metas = [];

    /**
     * getter 方法名缓存.
     */
    protected static array $__getterCache = [];

    /**
     * setter 方法名缓存.
     */
    protected static array $__setterCache = [];

    /**
     * 序列化后的所有字段属性名列表.
     */
    protected array $__fieldNames = [];

    /**
     * 当前对象 meta 缓存.
     *
     * @var \Imi\Model\Meta
     */
    protected Meta $__meta;

    /**
     * 真实类名.
     */
    protected ?string $__realClass = null;

    /**
     * 记录是否存在.
     */
    protected ?bool $__recordExists = null;

    /**
     * 序列化字段.
     *
     * @var array|null
     */
    protected $__serializedFields = null;

    /**
     * 原始数据.
     */
    protected array $__originData = [];

    public function __construct(array $data = [])
    {
        $this->__meta = $meta = static::__getMeta();
        $this->__fieldNames = $meta->getSerializableFieldNames();
        if (!$this instanceof IBean)
        {
            $this->__init($data);
        }
    }

    public function __init(array $data = []): void
    {
        $meta = $this->__meta;
        $isBean = $meta->isBean();
        if ($isBean)
        {
            // 初始化前
            $this->trigger(ModelEvents::BEFORE_INIT, [
                'model' => $this,
                'data'  => $data,
            ], $this, \Imi\Model\Event\Param\InitEventParam::class);
        }

        $this->__originData = $data;
        if ($data)
        {
            $fieldAnnotations = $meta->getFields();
            $dbFieldAnnotations = $meta->getDbFields();
            foreach ($data as $k => $v)
            {
                if (isset($fieldAnnotations[$k]))
                {
                    $fieldAnnotation = $fieldAnnotations[$k];
                }
                elseif (isset($dbFieldAnnotations[$k]))
                {
                    $item = $dbFieldAnnotations[$k];
                    $fieldAnnotation = $item['column'];
                    $k = $item['propertyName'];
                }
                else
                {
                    $fieldAnnotation = null;
                }
                if ($fieldAnnotation && \is_string($v))
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
                            if ('' === $v)
                            {
                                $v = [];
                            }
                            elseif (null !== $fieldAnnotation->listSeparator)
                            {
                                $v = explode($fieldAnnotation->listSeparator, $v);
                            }
                            break;
                    }
                }
                $this[$k] = $v;
            }
        }

        if ($isBean)
        {
            // 初始化后
            $this->trigger(ModelEvents::AFTER_INIT, [
                'model' => $this,
                'data'  => $data,
            ], $this, \Imi\Model\Event\Param\InitEventParam::class);
        }
    }

    /**
     * 实例化当前类.
     *
     * @param mixed ...$args
     *
     * @return static
     */
    public static function newInstance(...$args): object
    {
        if (static::__getMeta()->isBean())
        {
            // @phpstan-ignore-next-line
            return BeanFactory::newInstance(static::class, ...$args);
        }
        else
        {
            return new static(...$args);
        }
    }

    /**
     * 从记录创建模型对象
     *
     * @return static
     */
    public static function createFromRecord(array $data): self
    {
        $model = static::newInstance($data);
        $model->__recordExists = true;

        return $model;
    }

    /**
     * @param int|string $offset
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset): bool
    {
        if (isset($this->__originData[$offset]))
        {
            return true;
        }
        $class = $this->__meta->getRealModelClass();
        if (isset(self::$__getterCache[$class][$offset]))
        {
            $methodName = self::$__getterCache[$class][$offset];
            if (false === $methodName)
            {
                return false;
            }
        }
        else
        {
            $methodName = 'get' . ucfirst($this->__getCamelName($offset));
            if (method_exists($this, $methodName))
            {
                self::$__getterCache[$class][$offset] = $methodName;
            }
            else
            {
                self::$__getterCache[$class][$offset] = false;

                return false;
            }
        }

        return null !== $this->$methodName();
    }

    /**
     * @param int|string $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function &offsetGet($offset)
    {
        $getterExists = true;
        $class = ($this->__realClass ??= $this->__meta->getRealModelClass());
        if (isset(self::$__getterCache[$class][$offset]))
        {
            $methodName = self::$__getterCache[$class][$offset];
            if (false === $methodName)
            {
                $getterExists = false;
            }
        }
        else
        {
            $methodName = 'get' . ucfirst($this->__getCamelName($offset));
            if (method_exists($this, $methodName))
            {
                self::$__getterCache[$class][$offset] = $methodName;
            }
            else
            {
                self::$__getterCache[$class][$offset] = false;
                $getterExists = false;
            }
        }
        if ($getterExists)
        {
            $__methodReference = &self::$__methodReference;
            if (!isset($__methodReference[$class][$methodName]))
            {
                $refMethod = ReflectionContainer::getMethodReflection(static::class, $methodName);
                $__methodReference[$class][$methodName] = $refMethod->returnsReference();
            }
            if ($__methodReference[$class][$methodName])
            {
                return $this->$methodName();
            }
            else
            {
                $result = $this->$methodName();
            }
        }
        elseif (isset($this->__originData[$offset]))
        {
            $result = $this->__originData[$offset];
        }
        else
        {
            $result = null;
        }

        return $result;
    }

    /**
     * @param int|string $offset
     * @param mixed      $value
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value): void
    {
        $meta = $this->__meta;
        $fields = $meta->getFields();
        // 数据库bit类型字段处理
        if (isset($fields[$offset]))
        {
            $column = $fields[$offset];
        }
        elseif (isset($fields[$camelName = $this->__getCamelName($offset)]))
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

        $class = ($this->__realClass ??= $this->__meta->getRealModelClass());
        if (isset(self::$__setterCache[$class][$offset]))
        {
            $methodName = self::$__setterCache[$class][$offset];
            if (false === $methodName)
            {
                $this->__originData[$offset] = $value;

                return;
            }
        }
        else
        {
            $methodName = 'set' . ucfirst($this->__getCamelName($offset));
            if (method_exists($this, $methodName))
            {
                self::$__setterCache[$class][$offset] = $methodName;
            }
            else
            {
                self::$__setterCache[$class][$offset] = false;
                $this->__originData[$offset] = $value;

                return;
            }
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
     * @param int|string $offset
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset): void
    {
        if (isset($this->__fieldNames[$offset]))
        {
            unset($this->__fieldNames[$offset]);
        }
        elseif (false !== ($index = array_search($offset, $this->__fieldNames)))
        {
            unset($this->__fieldNames[$index]);
        }
        elseif (isset($this->__originData[$offset]))
        {
            unset($this->__originData[$offset]);
        }
    }

    /**
     * @return mixed
     */
    public function &__get(string $name)
    {
        return $this[$name];
    }

    /**
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        $this[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function __isset(string $name)
    {
        return isset($this[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function __unset(string $name): void
    {
        unset($this[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        $serializedFields = $this->__serializedFields;
        $result = [];
        if (null === $serializedFields)
        {
            $meta = $this->__meta;
            $realClass = ($this->__realClass ??= $meta->getRealModelClass());
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

        return json_decode(json_encode($data, \JSON_THROW_ON_ERROR), true, 512, \JSON_THROW_ON_ERROR);
    }

    /**
     * 转换模型数组为模型.
     *
     * @param bool $filter 过滤隐藏属性
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
     * @return mixed|false
     */
    #[\ReturnTypeWillChange]
    public function &current()
    {
        $value = $this[current($this->__fieldNames)];

        return $value;
    }

    /**
     * @return int|string|null
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return current($this->__fieldNames);
    }

    /**
     * {@inheritDoc}
     */
    public function next(): void
    {
        next($this->__fieldNames);
    }

    /**
     * {@inheritDoc}
     */
    public function rewind(): void
    {
        reset($this->__fieldNames);
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return false !== current($this->__fieldNames);
    }

    /**
     * {@inheritDoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * 从一个数组赋值到当前模型.
     */
    public function set(array $data): void
    {
        foreach ($data as $k => $v)
        {
            $this[$k] = $v;
        }
    }

    /**
     * 获取驼峰命名.
     */
    protected function __getCamelName(string $name): string
    {
        return self::$__camelCache[$name] ??= Text::toCamelName($name);
    }

    /**
     * 处理导出属性.
     *
     * @param \Imi\Model\Annotation\ExtractProperty[] $annotations
     */
    protected function __parseExtractProperty(string $propertyName, array $annotations): void
    {
        foreach ($annotations as $annotation)
        {
            if ('' === $annotation->alias)
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
    public static function __getMeta($object = null): Meta
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
     */
    public function __getSerializedFields(): ?array
    {
        return $this->__serializedFields;
    }

    /**
     * 设置模型序列化字段.
     */
    public function __setSerializedFields(?array $serializedFields): self
    {
        $this->__serializedFields = $serializedFields;

        return $this;
    }

    public function __getOriginData(): array
    {
        return $this->__originData;
    }
}
