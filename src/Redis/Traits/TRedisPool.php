<?php

declare(strict_types=1);

namespace Imi\Redis\Traits;

use Imi\Bean\BeanFactory;
use Imi\Redis\Enum\RedisMode;
use Imi\Redis\RedisHandler;
use Imi\Redis\RedisResource;
use InvalidArgumentException;
use RedisException;

trait TRedisPool
{
    /**
     * 创建资源.
     *
     * @return \Imi\Redis\RedisResource
     */
    protected function createResource(): \Imi\Pool\Interfaces\IPoolResource
    {
        $config = $this->getNextResourceConfig();
        $mode = $config['mode'] ?? RedisMode::STANDALONE;
        $class = $config['handlerClass'] ?? \Redis::class;
        switch ($mode)
        {
            case RedisMode::STANDALONE:
                $redis = new $class();
                break;
            case RedisMode::SENTINEL:
                $master = $config['master'] ?? '';
                $nodes = $config['nodes'] ?? [];
                shuffle($nodes);
                foreach ($nodes as $node)
                {
                    if (\is_array($node))
                    {
                        $host = $node['host'] ?? '127.0.0.1';
                        $port = $node['port'] ?? 6379;
                    }
                    else
                    {
                        [$host, $port] = explode(':', $node);
                    }
                    $redisSentinel = new \RedisSentinel($host, $port, $config['timeout'] ?? null, $config['persistent'] ?? null, $config['retryInterval'] ?? null, $config['readTimeout'] ?? null);
                    $masterArray = $redisSentinel->master($master);
                    if (\is_array($masterArray) && isset($masterArray['ip'], $masterArray['port']))
                    {
                        $config['host'] = $masterArray['ip'];
                        $config['port'] = $masterArray['port'];
                        $redis = new $class();
                        break;
                    }
                }
                if (!isset($redis))
                {
                    throw new RedisException('None of redis slave nodes are alive');
                }
                break;
            case RedisMode::CLUSTER:
                $redis = new \RedisCluster($config['name'] ?? null, $config['seeds'] ?? [], $config['timeout'] ?? null, $config['readTimeout'] ?? null, $config['persistent'] ?? null, $config['password'] ?? null);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Invalid mode %s', $mode));
        }

        return new RedisResource($this, BeanFactory::newInstance(RedisHandler::class, $redis), $config);
    }
}
