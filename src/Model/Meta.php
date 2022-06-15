<?php

declare(strict_types=1);

namespace Imi\Model;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\ExtractProperty;
use Imi\Model\Annotation\JsonDecode;
use Imi\Model\Annotation\JsonEncode;
use Imi\Model\Annotation\JsonNotNull;
use Imi\Model\Annotation\Serializable;
use Imi\Model\Annotation\Serializables;
use Imi\Model\Annotation\Sql;
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
     * 数据库名.
     */
    private ?string $databaseName = null;

    /**
     * 表名.
     */
    private ?string $tableName = null;

    /**
     * 使用表名前缀
     */
    private bool $usePrefix = false;

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
    private array $serializableFieldNames;

    /**
     * 数据库字段名和 Column 注解映射.
     */
    private array $dbFields;

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
     * 针对字段设置的 JSON 序列化时的配置.
     *
     * @var JsonEncode[]
     */
    private array $fieldsJsonEncode = [];

    /**
     * JSON 反序列化时的配置.
     */
    private ?JsonDecode $jsonDecode = null;

    /**
     * 针对字段设置的 JSON 反序列化时的配置.
     *
     * @var JsonDecode[]
     */
    private array $fieldsJsonDecode = [];

    /**
     * 定义 SQL 语句的字段列表.
     *
     * @var \Imi\Model\Annotation\Sql[][]
     */
    private array $sqlColumns;

    /**
     * 是否为继承父类的模型.
     */
    private bool $inherit = false;

    /**
     * 真实的模型类名.
     */
    private string $realModelClass = '';

    /**
     * 模型对象是否作为 bean 类使用.
     */
    private bool $bean = false;

    /**
     * 处理后的序列化字段数组.
     *
     * 已包含注解：Serializable、Serializables
     */
    private array $parsedSerializableFieldNames = [];

    public function __construct(string $modelClass, bool $inherit = false)
    {
        $id = [];
        $this->inherit = $inherit;
        if ($inherit)
        {
            $realModelClass = get_parent_class($modelClass);
        }
        else
        {
            $realModelClass = $modelClass;
        }
        $this->realModelClass = $realModelClass;
        $this->className = $modelClass;
        $annotations = AnnotationManager::getClassAnnotations($realModelClass, [
            Table::class,
            Entity::class,
            JsonEncode::class,
            JsonDecode::class,
            Serializables::class,
        ], true, true);
        /** @var \Imi\Model\Annotation\Table|null $table */
        $table = $annotations[Table::class];
        /** @var \Imi\Model\Annotation\Entity|null $entity */
        $entity = $annotations[Entity::class];
        $this->jsonEncode = $annotations[JsonEncode::class];
        $this->jsonDecode = $annotations[JsonDecode::class];
        /** @var Serializables|null $serializables */
        $serializables = $this->serializables = $annotations[Serializables::class];
        if ($table)
        {
            $this->dbPoolName = $table->dbPoolName;
            $this->id = $id = (array) $table->id;
            $this->setTableName($table->name);
            $this->usePrefix = $table->usePrefix;
        }
        $this->firstId = $id[0] ?? null;
        $fields = $dbFields = [];
        $annotations = AnnotationManager::getPropertiesAnnotations($realModelClass, [
            Column::class,
            Serializable::class,
            ExtractProperty::class,
            JsonNotNull::class,
            Sql::class,
            JsonEncode::class,
            JsonDecode::class,
        ]);
        foreach ($annotations[Column::class] as $name => $columns)
        {
            /** @var Column $column */
            $column = $columns[0];
            if (null !== $column->name)
            {
                $dbFields[$column->name] = [
                    'propertyName' => $name,
                    'column'       => $column,
                ];
            }
            $fields[$name] = $column;
        }
        /** @var Serializable[][] $serializableSets */
        $serializableSets = $this->serializableSets = $annotations[Serializable::class];
        $this->extractPropertys = $annotations[ExtractProperty::class];
        $this->propertyJsonNotNullMap = $annotations[JsonNotNull::class];
        $this->sqlColumns = $annotations[Sql::class];
        $this->fieldsJsonEncode = $annotations[JsonEncode::class];
        $this->fieldsJsonDecode = $annotations[JsonDecode::class];
        $this->relation = $relation = ModelRelationManager::hasRelation($realModelClass);
        if ($relation)
        {
            foreach (ModelRelationManager::getRelationFieldNames($realModelClass) as $name)
            {
                if (!isset($fields[$name]))
                {
                    $fields[$name] = new Column(['virtual' => true]);
                }
            }
        }
        $this->dbFields = $dbFields;
        $this->fields = $fields;
        $this->fieldNames = $fieldNames = array_keys($fields);
        $this->camel = $camel = $entity->camel ?? true;
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
        foreach ($fields as $field => $column)
        {
            if ($column->isAutoIncrement)
            {
                $this->autoIncrementField = $field;
                break;
            }
        }
        $this->bean = $entity->bean;
        $parsedSerializableFieldNames = [];
        foreach ($serializableFieldNames as $name)
        {
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
            $parsedSerializableFieldNames[] = $name;
        }
        $this->parsedSerializableFieldNames = $parsedSerializableFieldNames;
    }

    /**
     * Get 数据库名.
     */
    public function getDatabaseName(): ?string
    {
        return $this->databaseName;
    }

    /**
     * Get 表名.
     */
    public function getTableName(): ?string
    {
        return $this->tableName;
    }

    /**
     * 获取完整表名.
     */
    public function getFullTableName(): ?string
    {
        if (null === $this->tableName)
        {
            return null;
        }
        if (null === $this->databaseName)
        {
            return $this->tableName;
        }

        return $this->databaseName . '.' . $this->tableName;
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
    public function getSqlColumns(): array
    {
        return $this->sqlColumns;
    }

    /**
     * Get 数据库字段名和 Column 注解映射.
     */
    public function getDbFields(): array
    {
        return $this->dbFields;
    }

    /**
     * Get 序列化后的所有字段属性名列表.
     *
     * @return string[]
     */
    public function getSerializableFieldNames(): array
    {
        return $this->serializableFieldNames;
    }

    /**
     * Get 是否为继承父类的模型.
     */
    public function getInherit(): bool
    {
        return $this->inherit;
    }

    /**
     * Get 真实的模型类名.
     */
    public function getRealModelClass(): string
    {
        return $this->realModelClass;
    }

    /**
     * Set 表名.
     */
    public function setTableName(?string $tableName): self
    {
        if (null === $tableName)
        {
            $this->databaseName = $this->tableName = null;
        }
        else
        {
            $list = explode('.', $tableName, 2);
            if (isset($list[1]))
            {
                $this->databaseName = $list[0];
                $this->tableName = $list[1];
            }
            else
            {
                $this->databaseName = null;
                $this->tableName = $tableName;
            }
        }

        return $this;
    }

    /**
     * Set 数据库连接池名称.
     */
    public function setDbPoolName(?string $dbPoolName): self
    {
        $this->dbPoolName = $dbPoolName;

        return $this;
    }

    /**
     * 模型对象是否作为 bean 类使用.
     */
    public function isBean(): bool
    {
        return $this->bean;
    }

    /**
     * Get 针对字段设置的 JSON 序列化时的配置.
     *
     * @return JsonEncode[]
     */
    public function getFieldsJsonEncode(): array
    {
        return $this->fieldsJsonEncode;
    }

    /**
     * Get jSON 反序列化时的配置.
     */
    public function getJsonDecode(): ?JsonDecode
    {
        return $this->jsonDecode;
    }

    /**
     * Get 针对字段设置的 JSON 反序列化时的配置.
     *
     * @return JsonDecode[]
     */
    public function getFieldsJsonDecode(): array
    {
        return $this->fieldsJsonDecode;
    }

    /**
     * Get 处理后的序列化字段数组.
     */
    public function getParsedSerializableFieldNames(): array
    {
        return $this->parsedSerializableFieldNames;
    }

    /**
     * 是否使用表名前缀
     */
    public function isUsePrefix(): bool
    {
        return $this->usePrefix;
    }
}
