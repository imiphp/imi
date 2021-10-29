<?php

declare(strict_types=1);

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
     */
    protected bool $isSetSerializedFields = false;

    /**
     * 关联查询预加载字段.
     */
    protected ?array $withFields = null;

    /**
     * {@inheritDoc}
     */
    public function get(?string $className = null)
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
     * {@inheritDoc}
     */
    public function getArray(?string $className = null): array
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
        elseif (is_subclass_of($className, Model::class))
        {
            $list = [];
            $hasRelation = ModelRelationManager::hasRelation($className);
            foreach ($this->statementRecords as $item)
            {
                $object = $className::createFromRecord($item, false);
                if ($this->isSetSerializedFields)
                {
                    if (!isset($serializedFields))
                    {
                        $serializedFields = array_keys($item);
                    }
                    $object->__setSerializedFields($serializedFields);
                }
                if (!$hasRelation)
                {
                    $object->trigger(ModelEvents::AFTER_QUERY, [
                        'model' => $object,
                    ], $object, AfterQueryEventParam::class);
                }
                $list[] = $object;
            }
            if ($hasRelation)
            {
                ModelRelationManager::initModels($list, $this->withFields, $className);
                foreach ($list as $object)
                {
                    $object->trigger(ModelEvents::AFTER_QUERY, [
                        'model' => $object,
                    ], $object, AfterQueryEventParam::class);
                }
            }

            return $list;
        }
        else
        {
            $list = [];
            foreach ($this->statementRecords as $item)
            {
                $list[] = new $className($item);
            }

            return $list;
        }
    }

    /**
     * Get 是否设置模型序列化字段.
     */
    public function getIsSetSerializedFields(): bool
    {
        return $this->isSetSerializedFields;
    }

    /**
     * Set 是否设置模型序列化字段.
     *
     * @param bool $isSetSerializedFields 是否设置模型序列化字段
     */
    public function setIsSetSerializedFields(bool $isSetSerializedFields): self
    {
        $this->isSetSerializedFields = $isSetSerializedFields;

        return $this;
    }

    /**
     * Get 关联查询预加载字段.
     */
    public function getWithFields(): array
    {
        return $this->withFields;
    }

    /**
     * Set 关联查询预加载字段.
     */
    public function setWithFields(?array $withFields): self
    {
        $this->withFields = $withFields;

        return $this;
    }
}
