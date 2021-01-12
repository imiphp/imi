<?php

declare(strict_types=1);

namespace Imi;

use Imi\Event\Event;
use Imi\Server\ConnectContext\Event\Param\ConnectContextRestoreParam;

class ConnectContext
{
    private function __construct()
    {
    }

    /**
     * 为当前连接创建上下文.
     *
     * @return void
     */
    public static function create(array $data = [])
    {
        $requestContextData = RequestContext::getContext();
        if (!static::get() && $fd = ($requestContextData['fd'] ?? null))
        {
            static::use(function (array $contextData) use ($data, $fd, $requestContextData): array {
                if ($contextData)
                {
                    $contextData = array_merge($contextData, $data);
                }
                else
                {
                    $contextData = $data;
                }
                $contextData['fd'] = $fd;
                $contextData['__serverName'] = $requestContextData['server']->getName();

                return $contextData;
            }, $fd);
        }
    }

    /**
     * 从某个连接上下文中，加载到当前上下文或指定上下文中.
     *
     * @param int      $fromFd
     * @param int|null $toFd
     *
     * @return void
     */
    public static function load(int $fromFd, ?int $toFd = null)
    {
        if (!$toFd)
        {
            $toFd = RequestContext::get('fd');
            if (null === $toFd)
            {
                return;
            }
        }
        $data = static::getContext($fromFd);
        static::use(function (array $contextData) use ($data, $toFd): array {
            $contextData = $data;
            $contextData['fd'] = $toFd;

            return $contextData;
        }, $toFd);
    }

    /**
     * 销毁当前连接的上下文.
     *
     * @param int|null $fd
     *
     * @return void
     */
    public static function destroy(?int $fd = null)
    {
        if (!$fd)
        {
            $fd = RequestContext::get('fd');
            if (null === $fd)
            {
                return;
            }
        }
        /** @var \Imi\Server\ConnectContext\StoreHandler $store */
        $store = RequestContext::getServerBean('ConnectContextStore');
        if (($ttl = $store->getTtl()) > 0)
        {
            $store->delayDestroy((string) $fd, $ttl);
        }
        else
        {
            $store->destroy((string) $fd);
        }
    }

    /**
     * 判断当前连接上下文是否存在.
     *
     * @param int|null $fd
     *
     * @return bool
     */
    public static function exists(?int $fd = null): bool
    {
        if (!$fd)
        {
            $fd = RequestContext::get('fd');
            if (null === $fd)
            {
                return false;
            }
        }

        return RequestContext::getServerBean('ConnectContextStore')->exists((string) $fd);
    }

    /**
     * 获取上下文数据.
     *
     * @param string|null $name
     * @param mixed       $default
     * @param int|null    $fd
     *
     * @return mixed
     */
    public static function get(?string $name = null, $default = null, ?int $fd = null)
    {
        if (!$fd)
        {
            $fd = RequestContext::get('fd');
            if (null === $fd)
            {
                return $default;
            }
        }
        $data = RequestContext::getServerBean('ConnectContextStore')->read((string) $fd);
        if (null === $name)
        {
            return $data;
        }
        else
        {
            return $data[$name] ?? $default;
        }
    }

    /**
     * 设置上下文数据.
     *
     * @param string   $name
     * @param mixed    $value
     * @param int|null $fd
     *
     * @return void
     */
    public static function set(?string $name, $value, ?int $fd = null)
    {
        if (!$fd)
        {
            $fd = RequestContext::get('fd');
            if (null === $fd)
            {
                return;
            }
        }
        $store = RequestContext::getServerBean('ConnectContextStore');
        $fdStr = (string) $fd;
        $result = $store->lock($fdStr, function () use ($store, $name, $value, $fdStr) {
            $data = $store->read($fdStr);
            $data[$name] = $value;
            $store->save($fdStr, $data);
        });
        if (!$result)
        {
            throw new \RuntimeException('ConnectContext lock fail');
        }
    }

    /**
     * 批量设置上下文数据.
     *
     * @param array    $data
     * @param int|null $fd
     *
     * @return void
     */
    public static function muiltiSet(array $data, ?int $fd = null)
    {
        if (!$fd)
        {
            $fd = RequestContext::get('fd');
            if (null === $fd)
            {
                return;
            }
        }
        $store = RequestContext::getServerBean('ConnectContextStore');
        $fdStr = (string) $fd;
        $result = $store->lock($fdStr, function () use ($store, $data, $fdStr) {
            $storeData = $store->read($fdStr);
            foreach ($data as $name => $value)
            {
                $storeData[$name] = $value;
            }
            $store->save($fdStr, $storeData);
        });
        if (!$result)
        {
            throw new \RuntimeException('ConnectContext lock fail');
        }
    }

    /**
     * 使用回调并且自动加锁进行操作，回调用返回数据会保存进连接上下文.
     *
     * @param callable $callable
     * @param int|null $fd
     *
     * @return void
     */
    public static function use(callable $callable, ?int $fd = null)
    {
        if (!$fd)
        {
            $fd = RequestContext::get('fd');
            if (null === $fd)
            {
                return;
            }
        }
        $store = RequestContext::getServerBean('ConnectContextStore');
        $fdStr = (string) $fd;
        $store->lock($fdStr, function () use ($callable, $store, $fdStr) {
            $data = $store->read($fdStr);
            $result = $callable($data);
            if ($result)
            {
                $store->save($fdStr, $result);
            }
        });
    }

    /**
     * 获取当前上下文.
     *
     * @param int|null $fd
     *
     * @return array
     */
    public static function getContext(?int $fd = null): array
    {
        return static::get(null, null, $fd);
    }

    /**
     * 绑定一个标记到当前连接.
     *
     * @param string   $flag
     * @param int|null $fd
     *
     * @return void
     */
    public static function bind(string $flag, ?int $fd = null)
    {
        if (!$fd)
        {
            $fd = RequestContext::get('fd');
            if (null === $fd)
            {
                return;
            }
        }
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');
        $connectionBinder->bind($flag, $fd);
    }

    /**
     * 绑定一个标记到当前连接，如果已绑定返回false.
     *
     * @param string $flag
     * @param int    $fd
     *
     * @return bool
     */
    public static function bindNx(string $flag, ?int $fd = null)
    {
        if (!$fd)
        {
            $fd = RequestContext::get('fd');
            if (null === $fd)
            {
                return false;
            }
        }
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');

        return $connectionBinder->bindNx($flag, $fd);
    }

    /**
     * 取消绑定.
     *
     * @param string   $flag
     * @param int|null $keepTime 旧数据保持时间，null 则不保留
     *
     * @return void
     */
    public static function unbind(string $flag, ?int $keepTime = null)
    {
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');
        $connectionBinder->unbind($flag, $keepTime);
    }

    /**
     * 使用标记获取连接编号.
     *
     * @param string $flag
     *
     * @return int|null
     */
    public static function getFdByFlag(string $flag): ?int
    {
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');

        return $connectionBinder->getFdByFlag($flag);
    }

    /**
     * 使用标记获取连接编号.
     *
     * @param string[] $flag
     *
     * @return int[]
     */
    public static function getFdsByFlags(array $flags): array
    {
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');

        return $connectionBinder->getFdsByFlags($flags);
    }

    /**
     * 使用连接编号获取标记.
     *
     * @param int $fd
     *
     * @return string|null
     */
    public static function getFlagByFd(int $fd): ?string
    {
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');

        return $connectionBinder->getFlagByFd($fd);
    }

    /**
     * 使用连接编号获取标记.
     *
     * @param int[] $fds
     *
     * @return string[]
     */
    public static function getFlagsByFds(array $fds): array
    {
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');

        return $connectionBinder->getFlagsByFds($fds);
    }

    /**
     * 使用标记获取旧的连接编号.
     *
     * @param string $flag
     *
     * @return int|null
     */
    public static function getOldFdByFlag(string $flag): ?int
    {
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');

        return $connectionBinder->getOldFdByFlag($flag);
    }

    /**
     * 恢复标记对应连接中的数据.
     *
     * @param string   $flag
     * @param int|null $toFd
     *
     * @return void
     */
    public static function restore(string $flag, ?int $toFd = null)
    {
        $fromFd = static::getOldFdByFlag($flag);
        if (!$fromFd)
        {
            throw new \RuntimeException(sprintf('Not found fd of connection flag %s', $flag));
        }
        if (!$toFd)
        {
            $toFd = RequestContext::get('fd');
            if (null === $toFd)
            {
                return;
            }
        }
        static::load($fromFd, $toFd);
        static::bind($flag, $toFd);
        Event::trigger('IMI.CONNECT_CONTEXT.RESTORE', [
            'fromFd'    => $fromFd,
            'toFd'      => $toFd,
        ], null, ConnectContextRestoreParam::class);
    }
}
