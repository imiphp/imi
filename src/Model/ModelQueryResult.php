<?php

declare(strict_types=1);

namespace Imi\Model;

use Imi\Bean\BeanFactory;
use Imi\Db\Query\Result;
use Imi\Model\Event\Param\AfterQueryEventParam;

/**
 * 模型查询结果集类.
 */
class ModelQueryResult extends Result
{
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
    public function get(?string $className = null): mixed
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
            $isModel = false;
        }
        if ($isModel || is_subclass_of($className, Model::class))
        {
            $with = $this->with;
            $withField = $this->withField;
            /** @var Meta $meta */
            $meta = $className::__getMeta();
            if ($with)
            {
                if ($withField)
                {
                    $serializedFields = $withField;
                }
                else
                {
                    $serializedFields = $meta->getParsedSerializableFieldNames();
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
                /** @var Model $object */
                $object = $className::createFromRecord($record, false);
                $object->__setSerializedFields($serializedFields);
                if ($meta->hasRelation())
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
            }
            if ($meta->isBean())
            {
                $object->dispatch(new AfterQueryEventParam($object));
            }
        }
        else
        {
            $object = BeanFactory::newInstance($className);
            foreach ($record as $k => $v)
            {
                $object->{$k} = $v;
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
            $isModel = false;
        }
        $list = [];
        if ($isModel || is_subclass_of($className, Model::class))
        {
            /** @var Meta $meta */
            $meta = $className::__getMeta();
            $hasRelation = $meta->hasRelation();
            $isBean = $meta->isBean();
            $withField = $this->withField;
            $with = $this->with;
            if ($with)
            {
                if ($withField)
                {
                    $serializedFields = $withField;
                }
                else
                {
                    $serializedFields = $meta->getParsedSerializableFieldNames();
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
            }
            elseif ($withField)
            {
                $serializedFields = $withField;
            }
            else
            {
                $serializedFields = [];
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
                    $object->dispatch(new AfterQueryEventParam($object));
                }
            }
            if ($hasRelation)
            {
                ModelRelationManager::initModels($list, null, $with, $className);
                if ($isBean)
                {
                    foreach ($list as $object)
                    {
                        $object->dispatch(new AfterQueryEventParam($object));
                    }
                }
            }
        }
        else
        {
            foreach ($this->statementRecords as $item)
            {
                $list[] = $row = BeanFactory::newInstance($className, $item);
                foreach ($item as $k => $v)
                {
                    $row->{$k} = $v;
                }
            }
        }

        return $list;
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
