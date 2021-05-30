<?php

declare(strict_types=1);

namespace Imi\Model;

use Imi\App;
use Imi\Db\Db;
use Imi\Db\Query\Field;
use Imi\Db\Query\Interfaces\IQuery;
use Imi\Db\Query\Interfaces\IResult;
use Imi\Event\Event;
use Imi\Model\Annotation\Column;
use Imi\Model\Event\ModelEvents;
use Imi\Model\Event\Param\InitEventParam;
use Imi\Model\Relation\Query;
use Imi\Model\Relation\Update;
use Imi\Util\LazyArrayObject;
use Imi\Util\Text;

/**
 * 常用的数据库模型.
 */
abstract class Model extends BaseModel
{
    public function __init(array $data = []): void
    {
        if ($this->__meta->hasRelation())
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
     * @param string|object $object
     * @param string|null   $poolName  连接池名，为null则取默认
     * @param int|null      $queryType 查询类型；Imi\Db\Query\QueryType::READ/WRITE
     */
    public static function query($object = null, ?string $poolName = null, ?int $queryType = null): IQuery
    {
        $meta = static::__getMeta($object);

        return App::getBean(ModelQuery::class, null, $meta->getClassName(), $poolName ?? $meta->getDbPoolName(), $queryType);
    }

    /**
     * 返回一个数据库查询器，查询结果为数组，而不是当前类实例对象
     *
     * @param string|object $object
     * @param string|null   $poolName  连接池名，为null则取默认
     * @param int|null      $queryType 查询类型；Imi\Db\Query\QueryType::READ/WRITE
     */
    public static function dbQuery($object = null, ?string $poolName = null, ?int $queryType = null): IQuery
    {
        $meta = static::__getMeta($object);

        return Db::query($poolName ?? $meta->getDbPoolName(), null, $queryType)->from($meta->getTableName());
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
        if (!isset($ids[0]))
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
                        $query->whereRaw(new Field(null, null, $name) . '=:' . $name);
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
                        $query->whereRaw(new Field(null, null, $name) . '=:' . $name);
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
        $query = static::parseWhere(static::query(), $where);

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
            $data = static::parseSaveData(iterator_to_array($this), 'insert', $this);
        }
        elseif (!$data instanceof \ArrayAccess)
        {
            $data = new LazyArrayObject($data);
        }
        $query = static::query($this);
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
        $result = $query->alias($this->__realClass . ':insert:' . md5(implode(',', $keys)))->insert($data);
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
        $query = static::query($this);
        $meta = $this->__meta;
        if (null === $data)
        {
            $data = static::parseSaveData(iterator_to_array($this), 'update', $this);
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
        if (!isset($conditionId[0]))
        {
            throw new \RuntimeException('Use Model->update(), primary key can not be null');
        }
        $result = $query->alias($this->__realClass . ':update:' . md5(implode(',', $keys)), function (IQuery $query) use ($conditionId) {
            // @phpstan-ignore-next-line
            if ($conditionId)
            {
                // 主键条件加入
                foreach ($conditionId as $idName)
                {
                    $query->whereRaw(new Field(null, null, $idName) . '=:c_' . $idName);
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
            $query = static::parseWhere($query, $where);

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
            $query = static::parseWhere($query, $where);

            $updateData = static::parseSaveData($data, 'update');

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
        $query = static::query($this);
        $data = static::parseSaveData(iterator_to_array($this), 'save', $this);

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
            $result = $query->alias($this->__realClass . ':save:' . md5(implode(',', $keys)), function (IQuery $query) use ($meta) {
                // 主键条件加入
                $id = $meta->getId();
                if ($id)
                {
                    foreach ($id as $idName)
                    {
                        if (isset($this->$idName))
                        {
                            $query->whereRaw(new Field(null, null, $idName) . '=:' . $idName);
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
        $query = static::query($this);

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
        $result = $query->alias($this->__realClass . ':delete', function (IQuery $query) use ($id) {
            // 主键条件加入
            foreach ($id as $idName)
            {
                if (isset($this->$idName))
                {
                    $query->whereRaw(new Field(null, null, $idName) . '=:' . $idName);
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
    }

    /**
     * 初始化关联属性.
     *
     * @param string ...$names
     */
    public function initRelations(string ...$names): void
    {
        foreach ($names as $name)
        {
            Query::initRelations($this, $name);
        }
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
        $query = static::parseWhere($query, $where);

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
        $canUpdateTime = 'save' === $type || 'update' === $type;
        $objectIsObject = \is_object($object);
        foreach ($meta->getDbFields() as $dbFielName => $item)
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
                switch ($columnType)
                {
                    case 'date':
                        $value = date('Y-m-d');
                        break;
                    case 'time':
                        $value = date('H:i:s');
                        break;
                    case 'datetime':
                    case 'timestamp':
                        $value = date('Y-m-d H:i:s');
                        break;
                    case 'int':
                        $value = time();
                        break;
                    case 'bigint':
                        $value = (int) (microtime(true) * 1000);
                        break;
                    case 'year':
                        $value = (int) date('Y');
                        break;
                    default:
                        throw new \RuntimeException(sprintf('Column %s type is %s, can not updateTime', $dbFielName, $column->type));
                }
                if ($objectIsObject)
                {
                    $object->$dbFielName = $value;
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
                            $value = json_encode($value);
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
            $result[$dbFielName] = $value;
        }

        // 更新时无需更新主键
        if ('update' === $type)
        {
            foreach ($meta->getId() as $id)
            {
                unset($result[$id]);
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
