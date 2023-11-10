<?php

declare(strict_types=1);

namespace Imi\Swoole\Util;

use Imi\Config;

/**
 * 通道队列类-支持多进程.
 */
class CoroutineChannelManager
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * \Swoole\Coroutine\Channel 数组.
     *
     * @var \Swoole\Coroutine\Channel[]
     */
    protected static array $channels = [];

    /**
     * 是否初始化.
     */
    protected static bool $inited = false;

    public static function init(): void
    {
        foreach (Config::getAliases() as $alias)
        {
            $names = Config::get($alias . '.coroutineChannels');
            if ($names)
            {
                self::setNames($names);
            }
        }
        self::$inited = true;
    }

    /**
     * 增加对象名称.
     *
     * @param int $size 通道占用的内存的尺寸，单位为字节。最小值为64K，最大值没有限制
     */
    public static function addName(string $name, int $size = 0): void
    {
        static::$channels[$name] = new \Swoole\Coroutine\Channel($size);
    }

    /**
     * 设置对象名称.
     */
    public static function setNames(array $names): void
    {
        foreach ($names as $key => $args)
        {
            static::$channels[$key] = new \Swoole\Coroutine\Channel(...$args);
        }
    }

    /**
     * 获取所有对象名称.
     */
    public static function getNames(): array
    {
        return array_keys(static::$channels);
    }

    /**
     * 向通道写入数据
     * $data可以为任意PHP变量，当$data是非字符串类型时底层会自动进行串化
     * $data的尺寸超过8K时会启用临时文件存储数据
     * $data必须为非空变量，如空字符串、空数组、0、null、false
     * 写入成功返回true
     * 通道的空间不足时写入失败并返回false.
     */
    public static function push(string $name, mixed $data): bool
    {
        return static::getInstance($name)->push($data);
    }

    /**
     * 弹出数据
     * pop方法无需传入任何参数
     * 当通道内有数据时自动将数据弹出并还原为PHP变量
     * 当通道内没有任何数据时pop会失败并返回false.
     */
    public static function pop(string $name, float $timeout = 0): mixed
    {
        return static::getInstance($name)->pop($timeout);
    }

    /**
     * 获取通道的状态
     * 返回一个数组，缓冲通道将包括4项信息，无缓冲通道返回2项信息
     * consumer_num 消费者数量，表示当前通道为空，有N个协程正在等待其他协程调用push方法生产数据
     * producer_num 生产者数量，表示当前通道已满，有N个协程正在等待其他协程调用pop方法消费数据
     * queue_num 通道中的元素数量
     * queue_bytes 通道当前占用的内存字节数.
     */
    public static function stats(string $name): array
    {
        return static::getInstance($name)->stats();
    }

    /**
     * 关闭通道。并唤醒所有等待读写的协程。
     * 唤醒所有生产者协程，push方法返回false
     * 唤醒所有消费者协程，pop方法返回false.
     */
    public static function close(string $name): void
    {
        static::getInstance($name)->close();
    }

    /**
     * 获取实例.
     */
    public static function getInstance(string $name): \Swoole\Coroutine\Channel
    {
        $channels = &static::$channels;
        if (!isset($channels[$name]))
        {
            if (self::$inited)
            {
                throw new \RuntimeException(sprintf('GetInstance failed, %s is not found', $name));
            }
            else
            {
                self::init();
                if (!isset($channels[$name]))
                {
                    throw new \RuntimeException(sprintf('GetInstance failed, %s is not found', $name));
                }
            }
        }

        return $channels[$name];
    }
}
