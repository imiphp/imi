<?php
namespace Imi\Model;

use Imi\Pool\PoolManager;
use Imi\Util\MemoryTableManager;
use Imi\Model\Parser\ModelParser;

/**
 * Redis 模型
 */
abstract class RedisModel extends BaseModel
{
    /**
     * 默认的key
     * @var string
     */
    protected $key;

    /**
     * set时，设置的数据过期时间
     * @var int
     */
    protected $__ttl;

    public function __init($data = [])
    {
        parent::__init($data);
        $this->__ttl = ModelManager::getRedisEntity($this)->ttl;
    }

    /**
     * 查找一条记录
     * @param string|array $condition
     * @return static
     */
    public static function find($condition)
    {
        $key = static::generateKey($condition);
        $data = static::__getRedis()->get($key);
        return static::newInstance($data);
    }

    /**
     * 查询多条记录
     * @return static[]
     */
    public static function select(...$conditions)
    {
        $keys = [];
        foreach($conditions as $condition)
        {
            $keys[] = static::generateKey($condition);
        }
        $datas = static::__getRedis()->mGet($keys);
        $list = [];
        foreach($datas as $data)
        {
            if(null !== $data)
            {
                $list[] = static::newInstance($data);
            }
        }
        return $list;
    }

    /**
     * 保存记录
     * @return void
     */
    public function save()
    {
        $redis = static::__getRedis($this);
        if(null === $this->__ttl)
        {
            $redis->set($this->__getKey(), $this->toArray());
        }
        else
        {
            $redis->set($this->__getKey(), $this->toArray(), $this->__ttl);
        }
    }
    
    /**
     * 删除记录
     * @return void
     */
    public function delete()
    {
        static::__getRedis($this)->del($this->__getKey());
    }

    /**
     * 批量删除
     * @param string ...$conditions
     * @return void
     */
    public static function deleteBatch(...$conditions)
    {
        $keys = [];
        foreach($conditions as $condition)
        {
            $keys[] = static::generateKey($condition);
        }
        static::__getRedis()->del(...$keys);
    }

    /**
     * 获取键
     * @return string
     */
    public function __getKey()
    {
        $rule = ModelManager::getKeyRule($this);
        $result = $rule->rule;
        foreach($rule->paramNames as $paramName)
        {
            if(!isset($this[$paramName]))
            {
                throw new \Exception(sprintf('__getKey param %s does not exists', $paramName));
            }
            $result = str_replace('{' . $paramName . '}', $this[$paramName], $result);
        }
        return $result;
    }

    /**
     * 生成key
     * @param string|array $condition
     * @return string
     */
    public static function generateKey($condition)
    {
        if(is_string($condition))
        {
            return $condition;
        }
        else
        {
            $rule = ModelManager::getKeyRule(static::class);
            $result = $rule->rule;
            foreach($rule->paramNames as $paramName)
            {
                if(!isset($condition[$paramName]))
                {
                    throw new \Exception(sprintf('generateKey param %s does not exists', $paramName));
                }
                $result = str_replace('{' . $paramName . '}', $condition[$paramName], $result);
            }
            return $result;
        }
    }

    /**
     * 获取Redis操作对象
     * @param RedisModel $redisModel
     * @return \Redis
     */
    public static function __getRedis(RedisModel $redisModel = null)
    {
        $annotation = ModelManager::getRedisEntity(null === $redisModel ? static::class : $redisModel);
        $redis = PoolManager::getRequestContextResource($annotation->poolName)->getInstance();
        if(null !== $annotation->db)
        {
            $redis->select($annotation->db);
        }
        return $redis;
    }

    /**
     * Get the value of key
     * @return string
     */ 
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the value of key
     * @param string $key
     * @return self
     */ 
    public function setKey(string $key)
    {
        $this->key = $key;

        return $this;
    }
}