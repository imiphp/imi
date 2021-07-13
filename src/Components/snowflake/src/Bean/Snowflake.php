<?php

declare(strict_types=1);

namespace Imi\Snowflake\Bean;

use Imi\Bean\Annotation\Bean;

/**
 * @Bean("Snowflake")
 */
class Snowflake
{
    /**
     * 生成器列表.
     *
     * 例：
     * [
     *     'order' => [
     *         'datacenterId'   => null, // 数据中心ID，未空时为0
     *         'workerId'       => null, // 工作进程ID，为空时取当前进程ID
     *         'startTimeStamp' => null, // 开始时间戳，单位：毫秒
     *         'redisPool'      => null, // Redis 连接池名称，为空取默认连接池
     *     ]
     * ]
     */
    protected array $list = [];

    /**
     * Get List.
     */
    public function getList(): array
    {
        return $this->list;
    }

    /**
     * 使用名称获取配置.
     */
    public function getByName(string $name): ?array
    {
        return $this->list[$name] ?? null;
    }
}
