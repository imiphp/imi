<?php

namespace Imi\Model;

use Imi\Bean\BeanFactory;
use Imi\Db\Query\Result;
use Imi\Event\IEvent;
use Imi\Model\Event\ModelEvents;
use Imi\Model\Event\Param\AfterQueryEventParam;

/**
 * 模型查询结果集类.
 */
class ModelQueryResult extends Result
{
    /**
     * 是否设置模型序列化字段.
     *
     * @var bool
     */
    protected $isSetSerializedFields = false;

    /**
     * 返回一行数据，数组或对象，失败返回null.
     *
     * @param string|null $className 实体类名，为null则返回数组
     *
     * @return mixed|null
     */
    public function get($className = null)
    {
        if (!$this->isSuccess)
        {
            throw new \RuntimeException('Result is not success!');
        }
        $record = $this->statementRecords[0] ?? null;
        if (!$record)
        {
            return null;
        }

        if (null === $className)
        {
            $className = $this->modelClass;
        }
        if (null === $className)
        {
            return $record;
        }
        else
        {
            if (is_subclass_of($className, Model::class))
            {
                /** @var Model $object */
                $object = $className::createFromRecord($record);
                if ($this->isSetSerializedFields)
                {
                    $object->__setSerializedFields(array_keys($record));
                }
            }
            else
            {
                $object = BeanFactory::newInstance($className);
                foreach ($record as $k => $v)
                {
                    $object->$k = $v;
                }
            }
            /** @var IEvent $object */
            if (is_subclass_of($object, IEvent::class))
            {
                $object->trigger(ModelEvents::AFTER_QUERY, [
                    'model'      => $object,
                ], $object, AfterQueryEventParam::class);
            }

            return $object;
        }
    }

    /**
     * 返回数组，失败返回null.
     *
     * @param string $className 实体类名，为null则数组每个成员为数组
     *
     * @return array|null
     */
    public function getArray($className = null)
    {
        if (!$this->isSuccess)
        {
            throw new \RuntimeException('Result is not success!');
        }

        if (null === $className)
        {
            $className = $this->modelClass;
        }
        if (null === $className)
        {
            return $this->statementRecords;
        }
        else
        {
            $list = [];
            $isModelClass = is_subclass_of($className, Model::class);
            $supportIEvent = is_subclass_of($className, IEvent::class);
            foreach ($this->statementRecords as $item)
            {
                if ($isModelClass)
                {
                    $object = $className::createFromRecord($item);
                    if ($this->isSetSerializedFields)
                    {
                        if (!isset($serializedFields))
                        {
                            $serializedFields = array_keys($item);
                        }
                        $object->__setSerializedFields($serializedFields);
                    }
                }
                else
                {
                    $object = $item;
                }
                if ($supportIEvent)
                {
                    $object->trigger(ModelEvents::AFTER_QUERY, [
                        'model'      => $object,
                    ], $object, AfterQueryEventParam::class);
                }
                $list[] = $object;
            }

            return $list;
        }
    }

    /**
     * Get 是否设置模型序列化字段.
     *
     * @return bool
     */
    public function getIsSetSerializedFields(): bool
    {
        return $this->isSetSerializedFields;
    }

    /**
     * Set 是否设置模型序列化字段.
     *
     * @param bool $isSetSerializedFields 是否设置模型序列化字段
     *
     * @return self
     */
    public function setIsSetSerializedFields(bool $isSetSerializedFields): self
    {
        $this->isSetSerializedFields = $isSetSerializedFields;

        return $this;
    }
}
