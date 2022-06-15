<?php

declare(strict_types=1);

namespace Imi\Model;

use Imi\App;
use Imi\Bean\IBean;
use Imi\Db\Db;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IResult;
use Imi\Db\Query\QueryType;
use Imi\Db\Query\Raw;
use Imi\Event\Event;
use Imi\Model\Annotation\Column;
use Imi\Model\Contract\IModelQuery;
use Imi\Model\Event\ModelEvents;
use Imi\Model\Relation\Update;
use Imi\Util\Imi;
use Imi\Util\LazyArrayObject;
use InvalidArgumentException;

/**
 * 常用的数据库模型.
 */
abstract class Model extends BaseModel
{
    public const DEFAULT_QUERY_CLASS = ModelQuery::class;

    /**
     * 动态模型集合.
     */
    protected static array $__forks = [];

    /**
     * 设置给字段的 SQL 值集合.
     */
    protected array $__rawValues = [];

    public function __construct(array $data = [], bool $queryRelation = true)
    {
        $this->__meta = $meta = static::__getMeta();
        $this->__fieldNames = $meta->getSerializableFieldNames();
        $this->__parsedSerializedFields = $meta->getParsedSerializableFieldNames();
        if (!$this instanceof IBean)
        {
            $this->__init($data, $queryRelation);
        }
    }

    public function __init(array $data = [], bool $queryRelation = true): void
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
                            $fieldsJsonDecode ??= $meta->getFieldsJsonDecode();
                            if (isset($fieldsJsonDecode[$k][0]))
                            {
                                $realJsonDecode = $fieldsJsonDecode[$k][0];
                            }
                            else
                            {
                                $realJsonDecode = ($jsonEncode ??= ($meta->getJsonDecode() ?? false));
                            }
                            if ($realJsonDecode)
                            {
                                $value = json_decode($v, $realJsonDecode->associative, $realJsonDecode->depth, $realJsonDecode->flags);
                            }
                            else
                            {
                                $value = json_decode($v, true);
                            }
                            if (\JSON_ERROR_NONE === json_last_error())
                            {
                                if ($realJsonDecode)
                                {
                                    $wrap = $realJsonDecode->wrap;
                                    if ('' !== $wrap && (\is_array($value) || \is_object($value)))
                                    {
                                        if (class_exists($wrap))
                                        {
                                            $v = new $wrap($value);
                                        }
                                        else
                                        {
                                            $v = $wrap($value);
                                        }
                                    }
                                    else
                                    {
                                        $v = $value;
                                    }
                                }
                                elseif (\is_array($value) || \is_object($value))
                                {
                                    $v = new LazyArrayObject($value);
                                }
                                else
                                {
                                    $v = $value;
                                }
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
                        case 'set':
                            if ('' === $v)
                            {
                                $v = [];
                            }
                            else
                            {
                                $v = explode(',', $v);
                            }
                            break;
                    }
                }
                $this[$k] = $v;
            }
        }

        if ($queryRelation && $meta->hasRelation())
        {
            ModelRelationManager::initModel($this);
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
     * 返回一个查询器.
     *
     * @param string|null $poolName  连接池名，为null则取默认
     * @param int|null    $queryType 查询类型；Imi\Db\Query\QueryType::READ/WRITE
     */
    public static function query(?string $poolName = null, ?int $queryType = null, string $queryClass = self::DEFAULT_QUERY_CLASS): IModelQuery
    {
        $meta = static::__getMeta(static::__getRealClassName());

        return App::getBean($queryClass, null, $meta->getClassName(), $poolName ?? $meta->getDbPoolName(), $queryType);
    }

    /**
     * 返回一个数据库查询器，查询结果为数组，而不是当前类实例对象
     *
     * @param string|null $poolName  连接池名，为null则取默认
     * @param int|null    $queryType 查询类型；Imi\Db\Query\QueryType::READ/WRITE
     */
    public static function dbQuery(?string $poolName = null, ?int $queryType = null): IQuery
    {
        $meta = static::__getMeta(static::__getRealClassName());

        return Db::query($poolName ?? $meta->getDbPoolName(), null, $queryType)->table($meta->getTableName(), null, $meta->getDatabaseName());
    }

    /**
     * 判断记录是否存在.
     *
     * @param callable|mixed ...$ids
     */
    public static function exists(...$ids): bool
    {
        if (!$ids)
        {
            throw new InvalidArgumentException('Model::exists() must pass in parameters');
        }
        $query = static::dbQuery()->limit(1);
        if (\is_callable($ids[0]))
        {
            // 回调传入条件
            ($ids[0])($query);
        }
        else
        {
            // 传主键值
            if (\is_array($ids[0]))
            {
                // 键值数组where条件
                foreach ($ids[0] as $name => $value)
                {
                    $query->where($name, '=', $value);
                }
            }
            else
            {
                // 主键值
                foreach (static::__getMeta()->getId() as $i => $name)
                {
                    if (!isset($ids[$i]))
                    {
                        break;
                    }
                    $query->where($name, '=', $ids[$i]);
                }
            }
        }

        return (bool) Db::select('select exists(' . $query->buildSelectSql() . ')', $query->getBinds(), static::__getMeta(static::__getRealClassName())->getDbPoolName(), QueryType::READ)->getScalar();
    }

    /**
     * 查找一条记录.
     *
     * @param callable|mixed ...$ids
     *
     * @return static|null
     */
    public static function find(...$ids): ?self
    {
        if (!$ids)
        {
            return null;
        }
        $query = static::query()->limit(1);
        if (\is_callable($ids[0]))
        {
            // 回调传入条件
            ($ids[0])($query);
        }
        else
        {
            // 传主键值
            if (\is_array($ids[0]))
            {
                // 键值数组where条件
                foreach ($ids[0] as $name => $value)
                {
                    $query->where($name, '=', $value);
                }
            }
            else
            {
                // 主键值
                foreach (static::__getMeta()->getId() as $i => $name)
                {
                    if (!isset($ids[$i]))
                    {
                        break;
                    }
                    $query->where($name, '=', $ids[$i]);
                }
            }
        }

        $realClassName = static::__getRealClassName();
        // 查找前
        Event::trigger($realClassName . ':' . ModelEvents::BEFORE_FIND, [
            'ids'   => $ids,
            'query' => $query,
        ], null, \Imi\Model\Event\Param\BeforeFindEventParam::class);

        $result = $query->select()->get();

        // 查找后
        Event::trigger($realClassName . ':' . ModelEvents::AFTER_FIND, [
            'ids'   => $ids,
            'model' => &$result,
        ], null, \Imi\Model\Event\Param\AfterFindEventParam::class);

        return $result;
    }

    /**
     * 查询多条记录.
     *
     * @param array|callable|null $where
     *
     * @return static[]
     */
    public static function select($where = null): array
    {
        $realClassName = static::__getRealClassName();
        $query = static::query();
        if ($where)
        {
            self::parseWhere($query, $where);
        }

        // 查询前
        Event::trigger($realClassName . ':' . ModelEvents::BEFORE_SELECT, [
            'query' => $query,
        ], null, \Imi\Model\Event\Param\BeforeSelectEventParam::class);

        $result = $query->select()->getArray();

        // 查询后
        Event::trigger($realClassName . ':' . ModelEvents::AFTER_SELECT, [
            'result' => &$result,
        ], null, \Imi\Model\Event\Param\AfterSelectEventParam::class);

        return $result;
    }

    /**
     * 插入记录.
     *
     * @param mixed $data
     */
    public function insert($data = null): IResult
    {
        if (null === $data)
        {
            $data = self::parseSaveData(iterator_to_array($this), 'insert', $this);
        }
        elseif (!$data instanceof \ArrayAccess)
        {
            $data = new LazyArrayObject($data);
        }
        $query = static::query();
        $meta = $this->__meta;
        $isBean = $meta->isBean();
        if ($isBean)
        {
            // 插入前
            $this->trigger(ModelEvents::BEFORE_INSERT, [
                'model' => $this,
                'data'  => $data,
                'query' => $query,
            ], $this, \Imi\Model\Event\Param\BeforeInsertEventParam::class);
        }

        $result = $query->insert($data);
        if ($result->isSuccess() && ($autoIncrementField = $meta->getAutoIncrementField()))
        {
            $this[$autoIncrementField] = $result->getLastInsertId();
        }

        if ($isBean)
        {
            // 插入后
            $this->trigger(ModelEvents::AFTER_INSERT, [
                'model'  => $this,
                'data'   => $data,
                'result' => $result,
            ], $this, \Imi\Model\Event\Param\AfterInsertEventParam::class);
        }

        if ($meta->hasRelation())
        {
            // 子模型插入
            ModelRelationManager::insertModel($this);
        }
        $this->__recordExists = true;

        return $result;
    }

    /**
     * 更新记录.
     *
     * @param mixed $data
     */
    public function update($data = null): IResult
    {
        $query = static::query()->limit(1);
        $meta = $this->__meta;
        if (null === $data)
        {
            $data = self::parseSaveData(iterator_to_array($this), 'update', $this);
        }
        elseif (!$data instanceof \ArrayAccess)
        {
            $data = new LazyArrayObject($data);
        }
        $isBean = $meta->isBean();

        if ($isBean)
        {
            // 更新前
            $this->trigger(ModelEvents::BEFORE_UPDATE, [
                'model' => $this,
                'data'  => $data,
                'query' => $query,
            ], $this, \Imi\Model\Event\Param\BeforeUpdateEventParam::class);
        }

        $hasIdWhere = false;
        foreach ($meta->getId() as $idName)
        {
            if (isset($data[$idName]))
            {
                $query->where($idName, '=', $data[$idName]);
                $hasIdWhere = true;
            }
            elseif (isset($this[$idName]))
            {
                $query->where($idName, '=', $this[$idName]);
                $hasIdWhere = true;
            }
        }
        if (!$hasIdWhere)
        {
            throw new \RuntimeException('Use Model->update(), primary key can not be null');
        }

        $result = $query->update($data);

        if ($isBean)
        {
            // 更新后
            $this->trigger(ModelEvents::AFTER_UPDATE, [
                'model'  => $this,
                'data'   => $data,
                'result' => $result,
            ], $this, \Imi\Model\Event\Param\AfterUpdateEventParam::class);
        }

        if ($meta->hasRelation())
        {
            // 子模型更新
            ModelRelationManager::updateModel($this);
        }

        return $result;
    }

    /**
     * 批量更新.
     *
     * @deprecated 3.0
     *
     * @param mixed          $data
     * @param array|callable $where
     */
    public static function updateBatch($data, $where = null): ?IResult
    {
        $class = static::__getRealClassName();
        if (Update::hasUpdateRelation($class))
        {
            $query = static::dbQuery();
            if ($where)
            {
                self::parseWhere($query, $where);
            }

            $list = $query->select()->getArray();

            if ($list)
            {
                foreach ($list as $row)
                {
                    $model = static::createFromRecord($row);
                    $model->set($data);
                    $model->update();
                }
            }

            return null;
        }
        else
        {
            $query = static::query();
            if ($where)
            {
                self::parseWhere($query, $where);
            }

            $updateData = self::parseSaveData($data, 'update');

            // 更新前
            Event::trigger($class . ':' . ModelEvents::BEFORE_BATCH_UPDATE, [
                'data'  => $updateData,
                'query' => $query,
            ], null, \Imi\Model\Event\Param\BeforeBatchUpdateEventParam::class);

            $result = $query->update($updateData);

            // 更新后
            Event::trigger($class . ':' . ModelEvents::AFTER_BATCH_UPDATE, [
                'data'   => $updateData,
                'result' => $result,
            ], null, \Imi\Model\Event\Param\BeforeBatchUpdateEventParam::class);

            return $result;
        }
    }

    /**
     * 保存记录.
     */
    public function save(): IResult
    {
        $meta = $this->__meta;
        $query = static::query();
        $data = self::parseSaveData(iterator_to_array($this), 'save', $this);
        $isBean = $meta->isBean();

        if ($isBean)
        {
            // 保存前
            $this->trigger(ModelEvents::BEFORE_SAVE, [
                'model' => $this,
                'data'  => $data,
                'query' => $query,
            ], $this, \Imi\Model\Event\Param\BeforeSaveEventParam::class);
        }

        $recordExists = $this->__recordExists;

        // 当有自增字段时，根据自增字段值处理
        if (null === $recordExists)
        {
            $autoIncrementField = $meta->getAutoIncrementField();
            if (null !== $autoIncrementField)
            {
                $recordExists = ($data[$autoIncrementField] ?? 0) > 0;
            }
        }
        else
        {
            $autoIncrementField = null;
        }

        if (true === $recordExists)
        {
            $result = $this->update($data);
        }
        elseif (false === $recordExists)
        {
            $result = $this->insert($data);
        }
        else
        {
            foreach ($meta->getId() as $idName)
            {
                if (isset($data[$idName]))
                {
                    $query->where($idName, '=', $data[$idName]);
                }
                elseif (isset($this[$idName]))
                {
                    $query->where($idName, '=', $this[$idName]);
                }
            }
            $result = $query->replace($data);
            if ($result->isSuccess() && $autoIncrementField)
            {
                $this[$autoIncrementField] = $result->getLastInsertId();
            }
            $this->__recordExists = true;
        }

        if ($isBean)
        {
            // 保存后
            $this->trigger(ModelEvents::AFTER_SAVE, [
                'model'  => $this,
                'data'   => $data,
                'result' => $result,
            ], $this, \Imi\Model\Event\Param\AfterSaveEventParam::class);
        }

        return $result;
    }

    /**
     * 删除记录.
     */
    public function delete(): IResult
    {
        $query = static::query();
        $meta = $this->__meta;
        $isBean = $meta->isBean();

        if ($isBean)
        {
            // 删除前
            $this->trigger(ModelEvents::BEFORE_DELETE, [
                'model' => $this,
                'query' => $query,
            ], $this, \Imi\Model\Event\Param\BeforeDeleteEventParam::class);
        }

        $hasIdWhere = false;
        foreach ($meta->getId() as $idName)
        {
            if (isset($this[$idName]))
            {
                $query->where($idName, '=', $this[$idName]);
                $hasIdWhere = true;
            }
        }
        if (!$hasIdWhere)
        {
            throw new \RuntimeException('Use Model->delete(), primary key can not be null');
        }
        $result = $query->delete();

        if ($isBean)
        {
            // 删除后
            $this->trigger(ModelEvents::AFTER_DELETE, [
                'model'  => $this,
                'result' => $result,
            ], $this, \Imi\Model\Event\Param\AfterDeleteEventParam::class);
        }

        if ($meta->hasRelation())
        {
            // 子模型删除
            ModelRelationManager::deleteModel($this);
        }

        $this->__recordExists = false;

        return $result;
    }

    /**
     * 查询指定关联.
     *
     * @param string ...$names
     */
    public function queryRelations(string ...$names): void
    {
        ModelRelationManager::queryModelRelations($this, ...$names);

        // 关联字段加入序列化
        if ($this->__serializedFields)
        {
            $this->__serializedFields = array_merge($this->__serializedFields, $names);
        }
        else
        {
            $this->__serializedFields = array_merge($this->__fieldNames, $names);
        }
    }

    /**
     * 为一个列表查询指定关联.
     *
     * @param string ...$names
     */
    public static function queryRelationsList(iterable $list, string ...$names): iterable
    {
        ModelRelationManager::initModels($list, $names);

        if ($list)
        {
            /** @var self $model */
            $model = $list[0];
            $__serializedFields = $model->__serializedFields;
            // 关联字段加入序列化
            if ($__serializedFields)
            {
                $__serializedFields = array_merge($__serializedFields, $names);
            }
            else
            {
                $__serializedFields = array_merge($model->__fieldNames, $names);
            }
        }

        return $list;
    }

    /**
     * 批量删除.
     *
     * @deprecated 3.0
     *
     * @param array|callable $where
     */
    public static function deleteBatch($where = null): IResult
    {
        $realClassName = static::__getRealClassName();
        $query = static::query();
        if ($where)
        {
            self::parseWhere($query, $where);
        }

        // 删除前
        Event::trigger($realClassName . ':' . ModelEvents::BEFORE_BATCH_DELETE, [
            'query' => $query,
        ], null, \Imi\Model\Event\Param\BeforeBatchDeleteEventParam::class);

        $result = $query->delete();

        // 删除后
        Event::trigger($realClassName . ':' . ModelEvents::AFTER_BATCH_DELETE, [
            'result' => $result,
        ], null, \Imi\Model\Event\Param\BeforeBatchDeleteEventParam::class);

        return $result;
    }

    /**
     * 统计数量.
     */
    public static function count(string $field = '*'): int
    {
        return static::aggregate('count', $field);
    }

    /**
     * 求和.
     *
     * @return int|float
     */
    public static function sum(string $field)
    {
        return static::aggregate('sum', $field);
    }

    /**
     * 平均值
     *
     * @return int|float
     */
    public static function avg(string $field)
    {
        return static::aggregate('avg', $field);
    }

    /**
     * 最大值
     *
     * @return int|float
     */
    public static function max(string $field)
    {
        return static::aggregate('max', $field);
    }

    /**
     * 最小值
     *
     * @return int|float
     */
    public static function min(string $field)
    {
        return static::aggregate('min', $field);
    }

    /**
     * 聚合函数.
     *
     * @return mixed
     */
    public static function aggregate(string $functionName, string $fieldName, ?callable $queryCallable = null)
    {
        $query = static::query();
        if (null !== $queryCallable)
        {
            // 回调传入条件
            $queryCallable($query);
        }

        return $query->$functionName($fieldName);
    }

    /**
     * Fork 模型.
     *
     * @return class-string<static>
     */
    public static function fork(?string $tableName = null, ?string $poolName = null)
    {
        $forks = &self::$__forks;
        if (isset($forks[static::class][$tableName][$poolName]))
        {
            return $forks[static::class][$tableName][$poolName];
        }
        $namespace = Imi::getClassNamespace(static::class);
        if (null === $tableName)
        {
            $setTableName = '';
        }
        else
        {
            $setTableName = '$meta->setTableName(\'' . addcslashes($tableName, '\'\\') . '\');';
        }
        if (null === $poolName)
        {
            $setPoolName = '';
        }
        else
        {
            $setPoolName = '$meta->setDbPoolName(\'' . addcslashes($poolName, '\'\\') . '\');';
        }
        $class = str_replace('\\', '__', static::class . '\\' . md5($tableName . '\\' . $poolName));
        $extendsClass = static::class;
        Imi::eval(<<<PHP
        namespace {$namespace} {
            class {$class} extends \\{$extendsClass}
            {
                public static function __getMeta(\$object = null): \Imi\Model\Meta
                {
                    if (\$object)
                    {
                        \$class = \Imi\Bean\BeanFactory::getObjectClass(\$object);
                    }
                    else
                    {
                        \$class = static::__getRealClassName();
                    }
                    \$__metas = &self::\$__metas;
                    if (isset(\$__metas[\$class]))
                    {
                        \$meta = \$__metas[\$class];
                    }
                    else
                    {
                        \$meta = \$__metas[\$class] = new \Imi\Model\Meta(\$class, true);
                    }
                    if (static::class === \$class || is_subclass_of(\$class, static::class))
                    {
                        {$setTableName}
                        {$setPoolName}
                    }

                    return \$meta;
                }
            }
        }
        PHP);

        return $forks[static::class][$tableName][$poolName] = $namespace . '\\' . $class;
    }

    /**
     * 从记录创建模型对象
     *
     * @return static
     */
    public static function createFromRecord(array $data, bool $queryRelation = true): self
    {
        $model = static::newInstance($data, $queryRelation);
        $model->__recordExists = true;

        return $model;
    }

    /**
     * 处理where条件.
     *
     * @param mixed $where
     */
    private static function parseWhere(IQuery $query, $where): void
    {
        if (\is_callable($where))
        {
            // 回调传入条件
            $where($query);
        }
        elseif ($where)
        {
            foreach ($where as $k => $v)
            {
                if (\is_array($v))
                {
                    $operation = array_shift($v);
                    $query->where($k, $operation, $v[0]);
                }
                else
                {
                    $query->where($k, '=', $v);
                }
            }
        }
    }

    /**
     * @return mixed
     */
    protected static function parseDateTime(?string $columnType)
    {
        switch ($columnType)
        {
            case 'date':
                return date('Y-m-d');
            case 'time':
                return date('H:i:s');
            case 'datetime':
            case 'timestamp':
                return date('Y-m-d H:i:s');
            case 'int':
                return time();
            case 'bigint':
                return (int) (microtime(true) * 1000);
            case 'year':
                return (int) date('Y');
            default:
                return null;
        }
    }

    /**
     * 处理保存的数据.
     *
     * @param object|array $data
     * @param static|null  $object
     */
    private static function parseSaveData($data, string $type, ?self $object = null): LazyArrayObject
    {
        $meta = static::__getMeta($object);
        $realClassName = static::__getRealClassName();
        // 处理前
        Event::trigger($realClassName . ':' . ModelEvents::BEFORE_PARSE_DATA, [
            'data'   => &$data,
            'object' => &$object,
        ], null, \Imi\Model\Event\Param\BeforeParseDataEventParam::class);

        if (\is_object($data))
        {
            if (null === $object)
            {
                $object = $data;
            }
            $_data = [];
            foreach ($data as $k => $v)
            {
                $_data[$k] = $v;
            }
            $data = $_data;
        }
        $result = [];
        $isInsert = 'insert' === $type;
        $isUpdate = 'update' === $type;
        $isSave = 'save' === $type;
        if ($objectIsObject = \is_object($object))
        {
            $rawValues = $object->__rawValues;
            $object->__rawValues = [];
        }
        else
        {
            $rawValues = null;
        }
        foreach ($meta->getDbFields() as $dbFieldName => $item)
        {
            /** @var Column $column */
            ['propertyName' => $name, 'column' => $column] = $item;
            // 虚拟字段不参与数据库操作
            if ($column->virtual)
            {
                continue;
            }
            if ($rawValues)
            {
                if (isset($rawValues[$name]))
                {
                    $result[$dbFieldName] = new Raw($rawValues[$name]);
                    continue;
                }
                if (isset($rawValues[$dbFieldName]))
                {
                    $result[$dbFieldName] = new Raw($rawValues[$dbFieldName]);
                    continue;
                }
            }
            $columnType = $column->type;
            // 字段自动更新时间
            if (($column->updateTime && !$isInsert) || ($column->createTime && ($isInsert || ($isSave && $object && !$object->__recordExists))))
            {
                $value = static::parseDateTime($columnType);
                if (null === $value)
                {
                    throw new \RuntimeException(sprintf('Column %s type is %s, can not updateTime', $dbFieldName, $columnType));
                }
                if ($objectIsObject)
                {
                    $object->$dbFieldName = $value;
                }
            }
            elseif (\array_key_exists($name, $data))
            {
                $value = $data[$name];
            }
            elseif (\array_key_exists($dbFieldName, $data))
            {
                $value = $data[$dbFieldName];
            }
            else
            {
                if ($isUpdate)
                {
                    continue;
                }
                $value = null;
            }
            if (null === $value && !$column->nullable && 'json' !== $columnType)
            {
                continue;
            }
            switch ($columnType)
            {
                case 'json':
                    $fieldsJsonEncode ??= $meta->getFieldsJsonEncode();
                    if (isset($fieldsJsonEncode[$name][0]))
                    {
                        $realJsonEncode = $fieldsJsonEncode[$name][0];
                    }
                    else
                    {
                        $realJsonEncode = ($jsonEncode ??= ($meta->getJsonEncode() ?? false));
                    }
                    if (null === $value && $column->nullable)
                    {
                        // 当字段允许`null`时，使用原生`null`存储
                        $value = null;
                    }
                    elseif ($realJsonEncode)
                    {
                        $value = json_encode($value, $realJsonEncode->flags, $realJsonEncode->depth);
                    }
                    else
                    {
                        $value = json_encode($value, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);
                    }
                    break;
                case 'list':
                    if (null !== $value && null !== $column->listSeparator)
                    {
                        $value = implode($column->listSeparator, $value);
                    }
                    break;
                case 'set':
                    $value = implode(',', $value);
                    break;
            }
            $result[$dbFieldName] = $value;
        }

        // 更新时无需更新主键
        if ($isUpdate)
        {
            foreach ($meta->getId() as $id)
            {
                if (isset($result[$id]))
                {
                    unset($result[$id]);
                }
            }
        }

        $result = new LazyArrayObject($result);
        // 处理后
        Event::trigger($realClassName . ':' . ModelEvents::AFTER_PARSE_DATA, [
            'data'   => &$data,     // 待处理的原始数据
            'object' => &$object,   // 模型对象，注意可能为 null
            'result' => &$result,   // 最终保存的数据
        ], null, \Imi\Model\Event\Param\AfterParseDataEventParam::class);

        return $result;
    }

    /**
     * 设置字段的值为 sql，如果为null则清除设置.
     */
    public function __setRaw(string $field, ?string $sql): self
    {
        $this->__rawValues[$field] = $sql;

        return $this;
    }

    /**
     * 获取设置字段的 sql 值
     */
    public function __getRaw(string $field): ?string
    {
        return $this->__rawValues[$field] ?? null;
    }
}
