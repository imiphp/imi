<?php

declare(strict_types=1);

namespace Imi\Model;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\JsonNotNull;
use Imi\Model\Annotation\Serializable;
use Imi\Model\Annotation\Serializables;
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
    private string $className = '';

    /**
     * 表名.
     *
     * @var string|null
     */
    private ?string $tableName = null;

    /**
     * 数据库连接池名称.
     *
     * @var string|null
     */
    private ?string $dbPoolName = null;

    /**
     * 主键.
     *
     * @var array|null
     */
    private ?array $id = null;

    /**
     * 第一个主键.
     *
     * @var string|null
     */
    private ?string $firstId = null;

    /**
     * 字段配置.
     *
     * @var \Imi\Model\Annotation\Column[]
     */
    private array $fields = [];

    /**
     * 字段名列表.
     *
     * @var string[]
     */
    private array $fieldNames = [];

    /**
     * 字段名列表，会包含关联模型的导出字段.
     *
     * @var string[]
     */
    private array $realFieldNames = [];

    /**
     * 模型是否为驼峰命名.
     *
     * @var bool
     */
    private bool $camel = true;

    /**
     * 序列化注解.
     *
     * @var \Imi\Model\Annotation\Serializables|null
     */
    private ?Serializables $serializables;

    /**
     * 序列化注解列表.
     *
     * @var \Imi\Model\Annotation\Serializable[]
     */
    private array $serializableSets = [];

    /**
     * 提取属性注解.
     *
     * @var \Imi\Model\Annotation\ExtractProperty[][]
     */
    private array $extractPropertys = [];

    /**
     * 是否有关联.
     *
     * @var bool
     */
    private bool $relation = false;

    /**
     * 自增字段名.
     *
     * @var string|null
     */
    private ?string $autoIncrementField = null;

    /**
     * JsonNotNull 注解集合.
     *
     * @var \Imi\Model\Annotation\JsonNotNull[]
     */
    private array $propertyJsonNotNullMap = [];

    public function __construct(string $modelClass)
    {
        $this->className = $modelClass;
        /** @var \Imi\Model\Annotation\Table|null $table */
        $table = AnnotationManager::getClassAnnotations($modelClass, Table::class)[0] ?? null;
        /** @var \Imi\Model\Annotation\Entity|null $entity */
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
        $this->propertyJsonNotNullMap = AnnotationManager::getPropertiesAnnotations($modelClass, JsonNotNull::class);
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
     * @return string|null
     */
    public function getTableName(): ?string
    {
        return $this->tableName;
    }

    /**
     * Get 数据库连接池名称.
     *
     * @return string|null
     */
    public function getDbPoolName(): ?string
    {
        return $this->dbPoolName;
    }

    /**
     * Get 主键.
     *
     * @return array|null
     */
    public function getId(): ?array
    {
        return $this->id;
    }

    /**
     * Get 第一个主键.
     *
     * @return string|null
     */
    public function getFirstId(): ?string
    {
        return $this->firstId;
    }

    /**
     * Get 字段配置.
     *
     * @return \Imi\Model\Annotation\Column[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Get 字段名列表.
     *
     * @return string[]
     */
    public function getFieldNames(): array
    {
        return $this->fieldNames;
    }

    /**
     * Get 模型是否为驼峰命名.
     *
     * @return bool
     */
    public function isCamel(): bool
    {
        return $this->camel;
    }

    /**
     * Get 字段名列表，会包含关联模型的导出字段.
     *
     * @return string[]
     */
    public function getRealFieldNames(): array
    {
        return $this->realFieldNames;
    }

    /**
     * Get 是否有关联.
     *
     * @return bool
     */
    public function hasRelation(): bool
    {
        return $this->relation;
    }

    /**
     * Get 序列化注解.
     *
     * @return \Imi\Model\Annotation\Serializables|null
     */
    public function getSerializables(): ?Serializables
    {
        return $this->serializables;
    }

    /**
     * Get 提取属性注解.
     *
     * @return \Imi\Model\Annotation\ExtractProperty[][]
     */
    public function getExtractPropertys(): array
    {
        return $this->extractPropertys;
    }

    /**
     * Get 类名.
     *
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * Get 序列化注解列表.
     *
     * @return \Imi\Model\Annotation\Serializable[]
     */
    public function getSerializableSets(): array
    {
        return $this->serializableSets;
    }

    /**
     * Get 自增字段名.
     *
     * @return string|null
     */
    public function getAutoIncrementField(): ?string
    {
        return $this->autoIncrementField;
    }

    /**
     * Get jsonNotNull 注解集合.
     *
     * @return \Imi\Model\Annotation\JsonNotNull[]
     */
    public function getPropertyJsonNotNullMap(): array
    {
        return $this->propertyJsonNotNullMap;
    }
}
