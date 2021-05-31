<?php

declare(strict_types=1);

namespace Imi\Model;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\JsonEncode;
use Imi\Model\Annotation\JsonNotNull;
use Imi\Model\Annotation\Serializable;
use Imi\Model\Annotation\Sql;
use Imi\Model\Annotation\Serializables;
use Imi\Model\Annotation\Table;
use Imi\Util\Text;

/**
 * 模型元数据.
 */
class Meta
{
    /**
     * 类名.
     */
    private string $className = '';

    /**
     * 表名.
     */
    private ?string $tableName = null;

    /**
     * 数据库连接池名称.
     */
    private ?string $dbPoolName = null;

    /**
     * 主键.
     */
    private ?array $id = null;

    /**
     * 第一个主键.
     */
    private ?string $firstId = null;

    /**
     * 所有字段配置.
     *
     * @var \Imi\Model\Annotation\Column[]
     */
    private array $fields = [];

    /**
     * 所有字段属性名列表.
     *
     * @var string[]
     */
    private array $fieldNames = [];

    /**
     * 序列化后的所有字段属性名列表.
     *
     * @var string[]
     */
    private $serializableFieldNames;

    /**
     * 数据库字段名和 Column 注解映射.
     *
     * @var array
     */
    private $dbFields;

    /**
     * 模型是否为驼峰命名.
     */
    private bool $camel = true;

    /**
     * 序列化注解.
     */
    private ?Serializables $serializables;

    /**
     * 序列化注解列表.
     *
     * @var \Imi\Model\Annotation\Serializable[][]
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
     */
    private bool $relation = false;

    /**
     * 自增字段名.
     */
    private ?string $autoIncrementField = null;

    /**
     * JsonNotNull 注解集合.
     *
     * @var \Imi\Model\Annotation\JsonNotNull[][]
     */
    private array $propertyJsonNotNullMap = [];

    /**
     * JSON 序列化时的配置.
     */
    private ?JsonEncode $jsonEncode = null;

    /**
     * 定义 SQL 语句的字段列表.
     *
     * @var \Imi\Model\Annotation\Sql[][]
     */
    private $sqlColumns;

    public function __construct(string $modelClass)
    {
        $this->className = $modelClass;
        /** @var \Imi\Model\Annotation\Table|null $table */
        $table = AnnotationManager::getClassAnnotations($modelClass, Table::class)[0] ?? null;
        /** @var \Imi\Model\Annotation\Entity|null $entity */
        $entity = AnnotationManager::getClassAnnotations($modelClass, Entity::class)[0] ?? null;
        $this->jsonEncode = AnnotationManager::getClassAnnotations($modelClass, JsonEncode::class)[0] ?? null;
        if ($table)
        {
            $this->tableName = $table->name;
            $this->dbPoolName = $table->dbPoolName;
            $this->id = (array) $table->id;
        }
        $this->firstId = $this->id[0] ?? null;
        $fields = $dbFields = [];
        foreach (AnnotationManager::getPropertiesAnnotations($modelClass, Column::class) as $name => $columns)
        {
            /** @var Column $column */
            $column = $columns[0];
            if (isset($column->name))
            {
                $dbFields[$column->name] = [
                    'propertyName' => $name,
                    'column'       => $column,
                ];
            }
            $fields[$name] = $column;
        }
        $this->fields = $fields;
        $this->fieldNames = $fieldNames = array_keys($fields);
        $this->camel = $camel = $entity->camel ?? false;
        $serializableFieldNames = [];
        foreach ($fieldNames as $fieldName)
        {
            if ($camel)
            {
                $serializableFieldNames[$fieldName] = Text::toCamelName($fieldName);
            }
            else
            {
                $serializableFieldNames[$fieldName] = Text::toUnderScoreCase($fieldName);
            }
        }
        $this->serializableFieldNames = $serializableFieldNames;
        $this->dbFields = $dbFields;
        foreach ($fields as $field => $column)
        {
            if ($column->isAutoIncrement)
            {
                $this->autoIncrementField = $field;
                break;
            }
        }
        $this->serializables = ModelManager::getSerializables($modelClass);
        $this->serializableSets = AnnotationManager::getPropertiesAnnotations($modelClass, Serializable::class);
        $this->extractPropertys = ModelManager::getExtractPropertys($modelClass);
        $this->propertyJsonNotNullMap = AnnotationManager::getPropertiesAnnotations($modelClass, JsonNotNull::class);
        $this->sqlColumns = AnnotationManager::getPropertiesAnnotations($modelClass, Sql::class);
        $this->relation = ModelRelationManager::hasRelation($modelClass);
        if ($this->relation)
        {
            $this->fieldNames = array_merge($this->fieldNames, ModelRelationManager::getRelationFieldNames($modelClass));
        }
    }

    /**
     * Get 表名.
     */
    public function getTableName(): ?string
    {
        return $this->tableName;
    }

    /**
     * Get 数据库连接池名称.
     */
    public function getDbPoolName(): ?string
    {
        return $this->dbPoolName;
    }

    /**
     * Get 主键.
     */
    public function getId(): ?array
    {
        return $this->id;
    }

    /**
     * Get 第一个主键.
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
     */
    public function isCamel(): bool
    {
        return $this->camel;
    }

    /**
    /**
     * Get 是否有关联.
     */
    public function hasRelation(): bool
    {
        return $this->relation;
    }

    /**
     * Get 序列化注解.
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
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * Get 序列化注解列表.
     *
     * @return \Imi\Model\Annotation\Serializable[][]
     */
    public function getSerializableSets(): array
    {
        return $this->serializableSets;
    }

    /**
     * Get 自增字段名.
     */
    public function getAutoIncrementField(): ?string
    {
        return $this->autoIncrementField;
    }

    /**
     * Get jsonNotNull 注解集合.
     *
     * @return \Imi\Model\Annotation\JsonNotNull[][]
     */
    public function getPropertyJsonNotNullMap(): array
    {
        return $this->propertyJsonNotNullMap;
    }

    /**
     * Get JSON 序列化时的配置.
     */
    public function getJsonEncode(): ?JsonEncode
    {
        return $this->jsonEncode;
    }

    /**
     * Get 定义 SQL 语句的字段列表.
     *
     * @return \Imi\Model\Annotation\Sql[][]
     */
    public function getSqlColumns()
    {
        return $this->sqlColumns;
    }

    /**
     * Get 数据库字段名和 Column 注解映射.
     *
     * @return array
     */
    public function getDbFields()
    {
        return $this->dbFields;
    }

    /**
     * Get 序列化后的所有字段属性名列表.
     *
     * @return string[]
     */
    public function getSerializableFieldNames()
    {
        return $this->serializableFieldNames;
    }
}
