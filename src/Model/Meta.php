<?php

namespace Imi\Model;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;
use Imi\Model\Annotation\JsonEncode;
use Imi\Model\Annotation\JsonNotNull;
use Imi\Model\Annotation\Serializable;
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
     * 所有字段配置.
     *
     * @var \Imi\Model\Annotation\Column[]
     */
    private $fields;

    /**
     * 所有字段属性名列表.
     *
     * @var string[]
     */
    private $fieldNames;

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
     *
     * @var bool
     */
    private $camel;

    /**
     * 序列化注解.
     *
     * @var \Imi\Model\Annotation\Serializables|null
     */
    private $serializables;

    /**
     * 序列化注解列表.
     *
     * @var \Imi\Model\Annotation\Serializable[][]
     */
    private $serializableSets;

    /**
     * 提取属性注解.
     *
     * @var \Imi\Model\Annotation\ExtractProperty[][]
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
     * @var string|null
     */
    private $autoIncrementField;

    /**
     * JsonNotNull 注解集合.
     *
     * @var \Imi\Model\Annotation\JsonNotNull[][]
     */
    private $propertyJsonNotNullMap;

    /**
     * JSON 序列化时的配置.
     *
     * @var JsonEncode|null
     */
    private $jsonEncode;

    /**
     * 定义 SQL 语句的字段列表.
     *
     * @var \Imi\Model\Annotation\Sql[][]
     */
    private $sqlColumns;

    /**
     * @param string $modelClass
     */
    public function __construct($modelClass)
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
        $this->relation = ModelRelationManager::hasRelation($modelClass);
        if ($this->relation)
        {
            foreach (ModelRelationManager::getRelationFieldNames($modelClass) as $name)
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
        $this->serializables = ModelManager::getSerializables($modelClass);
        $this->serializableSets = AnnotationManager::getPropertiesAnnotations($modelClass, Serializable::class);
        $this->extractPropertys = ModelManager::getExtractPropertys($modelClass);
        $this->propertyJsonNotNullMap = AnnotationManager::getPropertiesAnnotations($modelClass, JsonNotNull::class);
        $this->sqlColumns = AnnotationManager::getPropertiesAnnotations($modelClass, Sql::class);
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
     * @return \Imi\Model\Annotation\Serializables|null
     */
    public function getSerializables()
    {
        return $this->serializables;
    }

    /**
     * Get 提取属性注解.
     *
     * @return \Imi\Model\Annotation\ExtractProperty[][]
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
     * @return \Imi\Model\Annotation\Serializable[][]
     */
    public function getSerializableSets()
    {
        return $this->serializableSets;
    }

    /**
     * Get 自增字段名.
     *
     * @return string|null
     */
    public function getAutoIncrementField()
    {
        return $this->autoIncrementField;
    }

    /**
     * Get jsonNotNull 注解集合.
     *
     * @return \Imi\Model\Annotation\JsonNotNull[][]
     */
    public function getPropertyJsonNotNullMap()
    {
        return $this->propertyJsonNotNullMap;
    }

    /**
     * Get JSON 序列化时的配置.
     *
     * @return \Imi\Model\Annotation\JsonEncode|null
     */
    public function getJsonEncode()
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
