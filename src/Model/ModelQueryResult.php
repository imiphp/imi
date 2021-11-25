<?php

declare(strict_types=1);

namespace Imi\Model;

use Imi\Bean\BeanFactory;
use Imi\Db\Query\Result;
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
    protected ?array $with = null;

    /**
     * 关联查询预加载字段.
     */
    protected ?array $withField = null;

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
            if (null === $className)
            {
                return $record;
            }
            $isModel = true;
        }
        else
        {
            $isModel = null;
        }
        if ($isModel || is_subclass_of($className, Model::class))
        {
            $with = $this->with;
            $withField = $this->withField;
            /** @var Meta $meta */
            $meta = $className::__getMeta();
            if ($with)
            {
                $hasRelation = $meta->hasRelation();
                if ($withField)
                {
                    $serializedFields = $withField;
                }
                else
                {
                    $serializedFields = [];
                    foreach ($with as $k => $v)
                    {
                        if (\is_string($k))
                        {
                            $serializedFields[] = $k;
                        }
                        else
                        {
                            $serializedFields[] = $v;
                        }
                    }
                    if ($this->isSetSerializedFields)
                    {
                        $serializedFields = array_merge($serializedFields, array_keys($record));
                    }
                    else
                    {
                        $serializedFields = array_merge($serializedFields, $className::__getMeta()->getSerializableFieldNames());
                    }
                }
                /** @var Model $object */
                $object = $className::createFromRecord($record, false);
                $object->__setSerializedFields($serializedFields);
                if ($hasRelation)
                {
                    ModelRelationManager::initModels([$object], null, $with, $className);
                }
            }
            else
            {
                /** @var Model $object */
                $object = $className::createFromRecord($record);
                if ($withField)
                {
                    $object->__setSerializedFields($withField);
                }
                elseif ($this->isSetSerializedFields)
                {
                    $object->__setSerializedFields(array_keys($record));
                }
            }
            if ($meta->isBean())
            {
                $object->trigger(ModelEvents::AFTER_QUERY, [
                    'model'      => $object,
                ], $object, AfterQueryEventParam::class);
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

        return $object;
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

        $statementRecords = $this->statementRecords;
        if (!$statementRecords)
        {
            return [];
        }
        if (null === $className)
        {
            $className = $this->modelClass;
            if (null === $className)
            {
                return $statementRecords;
            }
            $isModel = true;
        }
        else
        {
            $isModel = null;
        }
        if ($isModel || is_subclass_of($className, Model::class))
        {
            $list = [];
            /** @var Meta $meta */
            $meta = $className::__getMeta();
            $hasRelation = $meta->hasRelation();
            $isBean = $meta->isBean();
            $withField = $this->withField;
            $with = $this->with;
            if ($withField)
            {
                $serializedFields = $withField;
            }
            else
            {
                $serializedFields = [];
                if ($with)
                {
                    foreach ($with as $k => $v)
                    {
                        if (\is_string($k))
                        {
                            $serializedFields[] = $k;
                        }
                        else
                        {
                            $serializedFields[] = $v;
                        }
                    }
                }
                if ($this->isSetSerializedFields)
                {
                    if ($serializedFields)
                    {
                        $serializedFields = array_merge($serializedFields, array_keys($statementRecords[0]));
                    }
                    else
                    {
                        $serializedFields = array_keys($statementRecords[0]);
                    }
                }
                elseif ($serializedFields)
                {
                    $serializedFields = array_merge($serializedFields, $className::__getMeta()->getSerializableFieldNames());
                }
            }
            foreach ($statementRecords as $item)
            {
                $list[] = $object = $className::createFromRecord($item, false);
                if ($serializedFields)
                {
                    $object->__setSerializedFields($serializedFields);
                }
                if ($isBean && !$hasRelation)
                {
                    $object->trigger(ModelEvents::AFTER_QUERY, [
                        'model' => $object,
                    ], $object, AfterQueryEventParam::class);
                }
            }
            if ($hasRelation)
            {
                ModelRelationManager::initModels($list, null, $with, $className);
                if ($isBean)
                {
                    foreach ($list as $object)
                    {
                        $object->trigger(ModelEvents::AFTER_QUERY, [
                            'model' => $object,
                        ], $object, AfterQueryEventParam::class);
                    }
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
    public function getWith(): ?array
    {
        return $this->with;
    }

    /**
     * Set 关联查询预加载字段.
     */
    public function setWith(?array $with): self
    {
        $this->with = $with;

        return $this;
    }

    /**
     * Get 关联查询预加载字段.
     */
    public function getWithField(): ?array
    {
        return $this->withField;
    }

    /**
     * Set 关联查询预加载字段.
     */
    public function setWithField(?array $withField): self
    {
        $this->withField = $withField;

        return $this;
    }
}
