<?php

declare(strict_types=1);

namespace Imi\Model;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\Serializable;
use Imi\Model\Annotation\Table;

/**
 * 模型元数据.
 */
class Meta
{
    /**
     * 类名.
     *
     * @var string
     */
    private $className;

    /**
     * 表名.
     *
     * @var string
     */
    private $tableName;

    /**
     * 数据库连接池名称.
     *
     * @var string
     */
    private $dbPoolName;

    /**
     * 主键.
     *
     * @var array
     */
    private $id;

    /**
     * 第一个主键.
     *
     * @var string
     */
    private $firstId;

    /**
     * 字段配置.
     *
     * @var \Imi\Model\Annotation\Column[]
     */
    private $fields;

    /**
     * 字段名列表.
     *
     * @var string[]
     */
    private $fieldNames;

    /**
     * 字段名列表，会包含关联模型的导出字段.
     *
     * @var string[]
     */
    private $realFieldNames;

    /**
     * 模型是否为驼峰命名.
     *
     * @var bool
     */
    private $camel;

    /**
     * 序列化注解.
     *
     * @var \Imi\Model\Annotation\Serializables
     */
    private $serializables;

    /**
     * 序列化注解列表.
     *
     * @var \Imi\Model\Annotation\Serializable[]
     */
    private $serializableSets;

    /**
     * 提取属性注解.
     *
     * @var \Imi\Model\Annotation\ExtractProperty[]
     */
    private $extractPropertys;

    /**
     * 是否有关联.
     *
     * @var bool
     */
    private $relation;

    /**
     * 自增字段名.
     *
     * @var string
     */
    private $autoIncrementField;

    public function __construct($modelClass)
    {
        $this->className = $modelClass;
        /** @var \Imi\Model\Annotation\Table $table */
        $table = AnnotationManager::getClassAnnotations($modelClass, Table::class)[0] ?? null;
        /** @var \Imi\Model\Annotation\Entity $entity */
        $entity = AnnotationManager::getClassAnnotations($modelClass, Entity::class)[0] ?? null;
        if ($table)
        {
            $this->tableName = $table->name;
            $this->dbPoolName = $table->dbPoolName;
            $this->id = (array) $table->id;
        }
        $this->firstId = $this->id[0] ?? null;
        $this->fields = $fields = ModelManager::getFields($modelClass);
        $this->fieldNames = array_keys($fields);
        foreach ($fields as $field => $column)
        {
            if ($column->isAutoIncrement)
            {
                $this->autoIncrementField = $field;
                break;
            }
        }
        if ($entity)
        {
            $this->camel = $entity->camel;
        }
        $this->serializables = ModelManager::getSerializables($modelClass);
        $this->serializableSets = AnnotationManager::getPropertiesAnnotations($modelClass, Serializable::class);
        $this->extractPropertys = ModelManager::getExtractPropertys($modelClass);
        $this->relation = ModelRelationManager::hasRelation($modelClass);
        if ($this->relation)
        {
            $this->realFieldNames = array_merge($this->fieldNames, ModelRelationManager::getRelationFieldNames($modelClass));
        }
        else
        {
            $this->realFieldNames = $this->fieldNames;
        }
    }

    /**
     * Get 表名.
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Get 数据库连接池名称.
     *
     * @return string
     */
    public function getDbPoolName()
    {
        return $this->dbPoolName;
    }

    /**
     * Get 主键.
     *
     * @return array
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get 第一个主键.
     *
     * @return string
     */
    public function getFirstId()
    {
        return $this->firstId;
    }

    /**
     * Get 字段配置.
     *
     * @return \Imi\Model\Annotation\Column[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get 字段名列表.
     *
     * @return string[]
     */
    public function getFieldNames()
    {
        return $this->fieldNames;
    }

    /**
     * Get 模型是否为驼峰命名.
     *
     * @return bool
     */
    public function isCamel()
    {
        return $this->camel;
    }

    /**
     * Get 字段名列表，会包含关联模型的导出字段.
     *
     * @return string[]
     */
    public function getRealFieldNames()
    {
        return $this->realFieldNames;
    }

    /**
     * Get 是否有关联.
     *
     * @return bool
     */
    public function hasRelation()
    {
        return $this->relation;
    }

    /**
     * Get 序列化注解.
     *
     * @return \Imi\Model\Annotation\Serializables
     */
    public function getSerializables()
    {
        return $this->serializables;
    }

    /**
     * Get 提取属性注解.
     *
     * @return \Imi\Model\Annotation\ExtractProperty[]
     */
    public function getExtractPropertys()
    {
        return $this->extractPropertys;
    }

    /**
     * Get 类名.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Get 序列化注解列表.
     *
     * @return \Imi\Model\Annotation\Serializable[]
     */
    public function getSerializableSets()
    {
        return $this->serializableSets;
    }

    /**
     * Get 自增字段名.
     *
     * @return string
     */
    public function getAutoIncrementField()
    {
        return $this->autoIncrementField;
    }
}
