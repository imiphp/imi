<?php

declare(strict_types=1);

namespace Imi\Model;

use Imi\App;
use Imi\Db\Db;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IResult;
use Imi\Event\Event;
use Imi\Model\Annotation\Column;
use Imi\Model\Contract\IModelQuery;
use Imi\Model\Event\ModelEvents;
use Imi\Model\Event\Param\InitEventParam;
use Imi\Model\Relation\Update;
use Imi\Util\Imi;
use Imi\Util\LazyArrayObject;

/**
 * 常用的数据库模型.
 */
abstract class Model extends BaseModel
{
    public const DEFAULT_QUERY_CLASS = ModelQuery::class;

    /**
     * 动态模型集合.
     */
    protected static array $forks = [];

    public function __init(array $data = [], bool $queryRelation = true): void
    {
        if ($queryRelation && $this->__meta->hasRelation())
        {
            $this->one(ModelEvents::AFTER_INIT, function (InitEventParam $e) {
                ModelRelationManager::initModel($this);
            }, \Imi\Util\ImiPriority::IMI_MAX);
        }
        parent::__init($data);
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
        $realClassName = static::__getRealClassName();
        $query = static::query();
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
                $keys = array_keys($ids[0]);
                $bindValues = [];
                foreach ($ids[0] as $k => $v)
                {
                    $bindValues[':' . $k] = $v;
                }
                $query = $query->alias($realClassName . ':find:pk1:' . md5(implode(',', $keys)), function (IQuery $query) use ($keys) {
                    foreach ($keys as $name)
                    {
                        $query->whereRaw($query->fieldQuote($name) . '=:' . $name);
                    }
                    $query->limit(1);
                })->bindValues($bindValues);
            }
            else
            {
                // 主键值
                $id = static::__getMeta()->getId();
                $keys = [];
                $bindValues = [];
                foreach ($id as $i => $idName)
                {
                    if (!isset($ids[$i]))
                    {
                        break;
                    }
                    $keys[] = $idName;
                    $bindValues[':' . $idName] = $ids[$i];
                }
                $query = $query->alias($realClassName . ':find:pk2:' . md5(implode(',', $keys)), function (IQuery $query) use ($keys) {
                    foreach ($keys as $name)
                    {
                        $query->whereRaw($query->fieldQuote($name) . '=:' . $name);
                    }
                    $query->limit(1);
                })->bindValues($bindValues);
            }
        }

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
        $query = self::parseWhere(static::query(), $where);

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

        // 插入前
        $this->trigger(ModelEvents::BEFORE_INSERT, [
            'model' => $this,
            'data'  => $data,
            'query' => $query,
        ], $this, \Imi\Model\Event\Param\BeforeInsertEventParam::class);

        $keys = [];
        foreach ($data as $k => $v)
        {
            $keys[] = $k;
        }
        $result = $query->alias($this->__className . ':insert:' . md5(implode(',', $keys)))->insert($data);
        if ($result->isSuccess() && ($autoIncrementField = $meta->getAutoIncrementField()))
        {
            $this[$autoIncrementField] = $result->getLastInsertId();
        }

        // 插入后
        $this->trigger(ModelEvents::AFTER_INSERT, [
            'model'  => $this,
            'data'   => $data,
            'result' => $result,
        ], $this, \Imi\Model\Event\Param\AfterInsertEventParam::class);

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
        $query = static::query();
        $meta = $this->__meta;
        if (null === $data)
        {
            $data = self::parseSaveData(iterator_to_array($this), 'update', $this);
        }
        elseif (!$data instanceof \ArrayAccess)
        {
            $data = new LazyArrayObject($data);
        }

        // 更新前
        $this->trigger(ModelEvents::BEFORE_UPDATE, [
            'model' => $this,
            'data'  => $data,
            'query' => $query,
        ], $this, \Imi\Model\Event\Param\BeforeUpdateEventParam::class);

        $keys = [];
        foreach ($data as $k => $v)
        {
            $keys[] = $k;
        }
        $keys[] = '#'; // 分隔符

        $conditionId = $bindValues = [];
        $id = $meta->getId();
        if ($id)
        {
            foreach ($id as $idName)
            {
                if (isset($this->$idName))
                {
                    $bindValues[':c_' . $idName] = $this->$idName;
                    $keys[] = $conditionId[] = $idName;
                }
            }
        }
        if (!$conditionId)
        {
            throw new \RuntimeException('Use Model->update(), primary key can not be null');
        }
        $result = $query->alias($this->__className . ':update:' . md5(implode(',', $keys)), function (IQuery $query) use ($conditionId) {
            // @phpstan-ignore-next-line
            if ($conditionId)
            {
                // 主键条件加入
                foreach ($conditionId as $idName)
                {
                    $query->whereRaw($query->fieldQuote($idName) . '=:c_' . $idName);
                }
            }
            $query->limit(1);
        })->bindValues($bindValues)->update($data);

        // 更新后
        $this->trigger(ModelEvents::AFTER_UPDATE, [
            'model'  => $this,
            'data'   => $data,
            'result' => $result,
        ], $this, \Imi\Model\Event\Param\AfterUpdateEventParam::class);

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
     * @param mixed          $data
     * @param array|callable $where
     */
    public static function updateBatch($data, $where = null): ?IResult
    {
        $class = static::__getRealClassName();
        if (Update::hasUpdateRelation($class))
        {
            $query = static::dbQuery();
            $query = self::parseWhere($query, $where);

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
            $query = self::parseWhere($query, $where);

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

        // 保存前
        $this->trigger(ModelEvents::BEFORE_SAVE, [
            'model' => $this,
            'data'  => $data,
            'query' => $query,
        ], $this, \Imi\Model\Event\Param\BeforeSaveEventParam::class);

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
            $keys = [];
            foreach ($data as $k => $_)
            {
                $keys[] = $k;
            }
            $result = $query->alias($this->__className . ':save:' . md5(implode(',', $keys)), function (IQuery $query) use ($meta) {
                // 主键条件加入
                $id = $meta->getId();
                if ($id)
                {
                    foreach ($id as $idName)
                    {
                        if (isset($this->$idName))
                        {
                            $query->whereRaw($query->fieldQuote($idName) . '=:' . $idName);
                        }
                    }
                }
            })->replace($data);
            if ($result->isSuccess() && $autoIncrementField)
            {
                $this[$autoIncrementField] = $result->getLastInsertId();
            }
            $this->__recordExists = true;
        }

        // 保存后
        $this->trigger(ModelEvents::AFTER_SAVE, [
            'model'  => $this,
            'data'   => $data,
            'result' => $result,
        ], $this, \Imi\Model\Event\Param\AfterSaveEventParam::class);

        return $result;
    }

    /**
     * 删除记录.
     */
    public function delete(): IResult
    {
        $query = static::query();

        // 删除前
        $this->trigger(ModelEvents::BEFORE_DELETE, [
            'model' => $this,
            'query' => $query,
        ], $this, \Imi\Model\Event\Param\BeforeDeleteEventParam::class);

        $bindValues = [];
        $meta = $this->__meta;
        $id = $meta->getId();
        if ($id)
        {
            foreach ($id as $idName)
            {
                if (isset($this->$idName))
                {
                    $bindValues[$idName] = $this->$idName;
                }
            }
        }
        if (!$bindValues)
        {
            throw new \RuntimeException('Use Model->delete(), primary key can not be null');
        }
        $result = $query->alias($this->__className . ':delete', function (IQuery $query) use ($id) {
            // 主键条件加入
            foreach ($id as $idName)
            {
                if (isset($this->$idName))
                {
                    $query->whereRaw($query->fieldQuote($idName) . '=:' . $idName);
                }
            }
            $query->limit(1);
        })->bindValues($bindValues)->delete();

        // 删除后
        $this->trigger(ModelEvents::AFTER_DELETE, [
            'model'  => $this,
            'result' => $result,
        ], $this, \Imi\Model\Event\Param\AfterDeleteEventParam::class);

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

        // 提取属性支持
        $propertyAnnotations = $this->__meta->getExtractPropertys();
        foreach ($names as $name)
        {
            if (isset($propertyAnnotations[$name]))
            {
                $this->__parseExtractProperty($name, $propertyAnnotations[$name]);
            }
        }

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
    public static function queryRelationsList(array $list, string ...$names): array
    {
        ModelRelationManager::initModels($list, $names, null, true);

        return $list;
    }

    /**
     * 批量删除.
     *
     * @param array|callable $where
     */
    public static function deleteBatch($where = null): IResult
    {
        $realClassName = static::__getRealClassName();
        $query = static::query();
        $query = self::parseWhere($query, $where);

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
        $forks = &self::$forks;
        if (isset($forks[static::class][$tableName][$poolName]))
        {
            return $forks[static::class][$tableName][$poolName];
        }
        $extendsClass = static::class;
        $namespace = Imi::getClassNamespace($extendsClass);
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
        $class = str_replace('\\', '__', $extendsClass . '\\' . md5($tableName . '\\' . $poolName));
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
                    if (!isset(\$__metas[\$class]))
                    {
                        \$meta = \$__metas[\$class] = new \Imi\Model\Meta(\$class, true);
                    }
                    else
                    {
                        \$meta = \$__metas[\$class];
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
    private static function parseWhere(IQuery $query, $where): IQuery
    {
        if (null === $where)
        {
            return $query;
        }
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

        return $query;
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
     * @param object|array  $data
     * @param object|string $object
     */
    private static function parseSaveData($data, string $type, $object = null): LazyArrayObject
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
        $result = new LazyArrayObject();
        $isUpdate = 'update' === $type;
        $canUpdateTime = $isUpdate || 'save' === $type;
        $objectIsObject = \is_object($object);
        foreach ($meta->getDbFields() as $dbFieldName => $item)
        {
            /** @var Column $column */
            ['propertyName' => $name, 'column' => $column] = $item;
            // 虚拟字段不参与数据库操作
            if ($column->virtual)
            {
                continue;
            }
            $columnType = $column->type;
            // 字段自动更新时间
            if ($canUpdateTime && $column->updateTime)
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
            elseif (\array_key_exists($column->name, $data))
            {
                $value = $data[$column->name];
            }
            else
            {
                if ($isUpdate)
                {
                    continue;
                }
                $value = null;
            }
            if (null === $value && !$column->nullable)
            {
                continue;
            }
            switch ($columnType)
            {
                case 'json':
                    if (null !== $value)
                    {
                        if (!isset($jsonEncode))
                        {
                            $jsonEncode = $meta->getJsonEncode() ?? false;
                        }
                        if ($jsonEncode)
                        {
                            $value = json_encode($value, $jsonEncode->flags, $jsonEncode->depth);
                        }
                        else
                        {
                            $value = json_encode($value, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);
                        }
                    }
                    break;
                case 'list':
                    if (null !== $value && null !== $column->listSeparator)
                    {
                        $value = implode($column->listSeparator, $value);
                    }
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

        // 处理后
        Event::trigger($realClassName . ':' . ModelEvents::AFTER_PARSE_DATA, [
            'data'   => &$data,     // 待处理的原始数据
            'object' => &$object,   // 模型对象，注意可能为 null
            'result' => &$result,   // 最终保存的数据
        ], null, \Imi\Model\Event\Param\AfterParseDataEventParam::class);

        return $result;
    }
}
