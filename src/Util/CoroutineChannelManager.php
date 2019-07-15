<?php
namespace Imi\Util;

/**
 * 通道队列类-支持多进程
 */
abstract class CoroutineChannelManager
{
    /**
     * \Swoole\Coroutine\Channel 数组
     * @var \Swoole\Coroutine\Channel[]
     */
    protected static $channels = [];
    
    /**
     * 增加对象名称
     * @param string $name
     * @param int $size 通道占用的内存的尺寸，单位为字节。最小值为64K，最大值没有限制
     * @return void
     */
    public static function addName(string $name, int $size = 0)
    {
        static::$channels[$name] = new \Swoole\Coroutine\Channel($size);
    }

    /**
     * 设置对象名称
     * @param string[] $names
     * @return void
     */
    public static function setNames(array $names)
    {
        foreach($names as $key => $args)
        {
            static::$channels[$key] = new \Swoole\Coroutine\Channel(...$args);
        }
    }

    /**
     * 获取所有对象名称
     * @return void
     */
    public static function getNames()
    {
        return array_keys(static::$channels);
    }

    /**
     * 向通道写入数据
     * $data可以为任意PHP变量，当$data是非字符串类型时底层会自动进行串化
     * $data的尺寸超过8K时会启用临时文件存储数据
     * $data必须为非空变量，如空字符串、空数组、0、null、false
     * 写入成功返回true
     * 通道的空间不足时写入失败并返回false
     * @param string $name
     * @param mixed $data
     * @return boolean
     */
    public static function push(string $name, $data)
    {
        return static::getInstance($name)->push($data);
    }

    /**
     * 弹出数据
     * pop方法无需传入任何参数
     * 当通道内有数据时自动将数据弹出并还原为PHP变量
     * 当通道内没有任何数据时pop会失败并返回false
     * @param string $name
     * @param float $timeout
     * @return mixed
     */
    public static function pop(string $name, $timeout = 0)
    {
        return static::getInstance($name)->pop($timeout);
    }
    
    /**
     * 获取通道的状态
     * 返回一个数组，缓冲通道将包括4项信息，无缓冲通道返回2项信息
     * consumer_num 消费者数量，表示当前通道为空，有N个协程正在等待其他协程调用push方法生产数据
     * producer_num 生产者数量，表示当前通道已满，有N个协程正在等待其他协程调用pop方法消费数据
     * queue_num 通道中的元素数量
     * queue_bytes 通道当前占用的内存字节数
     * @param string $name
     * @return array
     */
    public static function stats(string $name): array
    {
        return static::getInstance($name)->stats();
    }

    /**
     * 关闭通道。并唤醒所有等待读写的协程。
     * 唤醒所有生产者协程，push方法返回false
     * 唤醒所有消费者协程，pop方法返回false
     * @param string $name
     * @return void
     */
    public static function close(string $name)
    {
        static::getInstance($name)->close();
    }

    /**
     * 通道读写检测。类似于socket_select和stream_select可以检测channel是否可进行读写。
     * 当$read或$write数组中有部分channel对象处于可读或可写状态，select会立即返回，不会产生协程调度。当数组中没有任何channel可读或可写时，将挂起当前协程，并设置定时器。当其中一个通道可读或可写时，将重新唤醒当前协程。
     * select操作只检测channel列表的可读或可写状态，但并不会读写channel，在select调用返回后，可遍历$read和$write数组，执行pop和push方法，完成通道读写操作。
     * 成功返回true，底层会修改$read、$write数组，$read和$write中的元素，即是可读或可写的channel
     * 超时或传入的参数错误，如$read和$write中有非channel对象，底层返回false
     * @param string $name
     * @param array $read 数组引用类型，元素为channel对象，读操作检测，可以为null
     * @param array $write 数组引用类型，元素为channel对象，写操作检测，可以为null
     * @param float $timeout 浮点型，超时设置，单位为秒，最小粒度为0.001秒，即1ms。默认为0，表示永不超时。
     * @return mixed
     */
    public static function select(string $name, array &$read, array &$write, float $timeout = 0)
    {
        return static::getInstance($name)->select($name, $read, $write, $timeout);
    }

    /**
     * 获取实例
     * @param string $name
     * @return \Swoole\Atomic
     */
    public static function getInstance(string $name): \Swoole\Coroutine\Channel
    {
        if(!isset(static::$channels[$name]))
        {
            throw new \RuntimeException(sprintf('GetInstance failed, %s is not found', $name));
        }
        return static::$channels[$name];
    }
}