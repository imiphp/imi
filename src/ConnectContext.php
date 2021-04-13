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
     */
    public static function create(array $data = []): void
    {
        $requestContextData = RequestContext::getContext();
        if (!static::get() && $clientId = ($requestContextData['clientId'] ?? null))
        {
            static::use(function (array $contextData) use ($data, $clientId, $requestContextData): array {
                if ($contextData)
                {
                    $contextData = array_merge($contextData, $data);
                }
                else
                {
                    $contextData = $data;
                }
                $contextData['clientId'] = $clientId;
                $contextData['__serverName'] = $requestContextData['server']->getName();

                return $contextData;
            }, $clientId);
        }
    }

    /**
     * 从某个连接上下文中，加载到当前上下文或指定上下文中.
     */
    public static function load(int $fromClientId, ?int $toClientId = null): void
    {
        if (!$toClientId)
        {
            $toClientId = self::getClientId();
            if (null === $toClientId)
            {
                return;
            }
        }
        $data = static::getContext($fromClientId);
        static::use(function (array $contextData) use ($data, $toClientId): array {
            $contextData = $data;
            $contextData['clientId'] = $toClientId;

            return $contextData;
        }, $toClientId);
    }

    /**
     * 销毁当前连接的上下文.
     *
     * @param int|string|null $clientId
     */
    public static function destroy($clientId = null): void
    {
        if (!$clientId)
        {
            $clientId = self::getClientId();
            if (null === $clientId)
            {
                return;
            }
        }
        /** @var \Imi\Server\ConnectContext\StoreHandler $store */
        $store = RequestContext::getServerBean('ConnectContextStore');
        if (($ttl = $store->getTtl()) > 0)
        {
            $store->delayDestroy((string) $clientId, $ttl);
        }
        else
        {
            $store->destroy((string) $clientId);
        }
    }

    /**
     * 判断当前连接上下文是否存在.
     *
     * @param int|string|null $clientId
     */
    public static function exists($clientId = null): bool
    {
        if (!$clientId)
        {
            $clientId = self::getClientId();
            if (null === $clientId)
            {
                return false;
            }
        }

        return RequestContext::getServerBean('ConnectContextStore')->exists((string) $clientId);
    }

    /**
     * 获取上下文数据.
     *
     * @param mixed           $default
     * @param int|string|null $clientId
     *
     * @return mixed
     */
    public static function get(?string $name = null, $default = null, $clientId = null)
    {
        if (!$clientId)
        {
            $clientId = self::getClientId();
            if (null === $clientId)
            {
                return $default;
            }
        }
        $data = RequestContext::getServerBean('ConnectContextStore')->read((string) $clientId);
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
     * @param string          $name
     * @param mixed           $value
     * @param int|string|null $clientId
     */
    public static function set(?string $name, $value, $clientId = null): void
    {
        if (!$clientId)
        {
            $clientId = self::getClientId();
            if (null === $clientId)
            {
                return;
            }
        }
        $store = RequestContext::getServerBean('ConnectContextStore');
        $clientIdStr = (string) $clientId;
        $result = $store->lock($clientIdStr, function () use ($store, $name, $value, $clientIdStr) {
            $data = $store->read($clientIdStr);
            $data[$name] = $value;
            $store->save($clientIdStr, $data);
        });
        if (!$result)
        {
            throw new \RuntimeException('ConnectContext lock fail');
        }
    }

    /**
     * 批量设置上下文数据.
     *
     * @param int|string|null $clientId
     */
    public static function muiltiSet(array $data, $clientId = null): void
    {
        if (!$clientId)
        {
            $clientId = self::getClientId();
            if (null === $clientId)
            {
                return;
            }
        }
        $store = RequestContext::getServerBean('ConnectContextStore');
        $clientIdStr = (string) $clientId;
        $result = $store->lock($clientIdStr, function () use ($store, $data, $clientIdStr) {
            $storeData = $store->read($clientIdStr);
            foreach ($data as $name => $value)
            {
                $storeData[$name] = $value;
            }
            $store->save($clientIdStr, $storeData);
        });
        if (!$result)
        {
            throw new \RuntimeException('ConnectContext lock fail');
        }
    }

    /**
     * 使用回调并且自动加锁进行操作，回调用返回数据会保存进连接上下文.
     *
     * @param int|string|null $clientId
     */
    public static function use(callable $callable, $clientId = null): void
    {
        if (!$clientId)
        {
            $clientId = self::getClientId();
            if (null === $clientId)
            {
                return;
            }
        }
        $store = RequestContext::getServerBean('ConnectContextStore');
        $clientIdStr = (string) $clientId;
        $store->lock($clientIdStr, function () use ($callable, $store, $clientIdStr) {
            $data = $store->read($clientIdStr);
            $result = $callable($data);
            if ($result)
            {
                $store->save($clientIdStr, $result);
            }
        });
    }

    /**
     * 获取当前上下文.
     *
     * @param int|string|null $clientId
     */
    public static function getContext($clientId = null): array
    {
        return static::get(null, null, $clientId);
    }

    /**
     * 绑定一个标记到当前连接.
     *
     * @param int|string|null $clientId
     */
    public static function bind(string $flag, $clientId = null): void
    {
        if (!$clientId)
        {
            $clientId = self::getClientId();
            if (null === $clientId)
            {
                return;
            }
        }
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');
        $connectionBinder->bind($flag, $clientId);
    }

    /**
     * 绑定一个标记到当前连接，如果已绑定返回false.
     *
     * @param int|string|null $clientId
     */
    public static function bindNx(string $flag, $clientId = null): bool
    {
        if (!$clientId)
        {
            $clientId = self::getClientId();
            if (null === $clientId)
            {
                return false;
            }
        }
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');

        return $connectionBinder->bindNx($flag, $clientId);
    }

    /**
     * 取消绑定.
     *
     * @param int|string $clientId
     * @param int|null   $keepTime 旧数据保持时间，null 则不保留
     */
    public static function unbind(string $flag, $clientId, ?int $keepTime = null): void
    {
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');
        $connectionBinder->unbind($flag, $clientId, $keepTime);
    }

    /**
     * 使用标记获取连接编号.
     *
     * @return array
     */
    public static function getClientIdByFlag(string $flag)
    {
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');

        return $connectionBinder->getClientIdByFlag($flag);
    }

    /**
     * 使用标记获取连接编号.
     *
     * @param string[] $flags
     */
    public static function getClientIdsByFlags(array $flags): array
    {
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');

        return $connectionBinder->getClientIdsByFlags($flags);
    }

    /**
     * 使用连接编号获取标记.
     *
     * @param int|string $clientId
     */
    public static function getFlagByClientId($clientId): ?string
    {
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');

        return $connectionBinder->getFlagByClientId($clientId);
    }

    /**
     * 使用连接编号获取标记.
     *
     * @param int[]|string[] $clientIds
     *
     * @return string[]
     */
    public static function getFlagsByClientIds(array $clientIds): array
    {
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');

        return $connectionBinder->getFlagsByClientIds($clientIds);
    }

    /**
     * 使用标记获取旧的连接编号.
     */
    public static function getOldClientIdByFlag(string $flag): ?int
    {
        /** @var \Imi\Server\ConnectContext\ConnectionBinder $connectionBinder */
        $connectionBinder = App::getBean('ConnectionBinder');

        return $connectionBinder->getOldClientIdByFlag($flag);
    }

    /**
     * 恢复标记对应连接中的数据.
     */
    public static function restore(string $flag, ?int $toClientId = null): void
    {
        $fromClientId = static::getOldClientIdByFlag($flag);
        if (!$fromClientId)
        {
            throw new \RuntimeException(sprintf('Not found clientId of connection flag %s', $flag));
        }
        if (!$toClientId)
        {
            $toClientId = self::getClientId();
            if (null === $toClientId)
            {
                return;
            }
        }
        static::load($fromClientId, $toClientId);
        static::bind($flag, $toClientId);
        Event::trigger('IMI.CONNECT_CONTEXT.RESTORE', [
            'fromClientId'    => $fromClientId,
            'toClientId'      => $toClientId,
        ], null, ConnectContextRestoreParam::class);
    }

    /**
     * 获取当前连接号.
     *
     * @return int|string|null
     */
    public static function getClientId()
    {
        return RequestContext::get('clientId');
    }
}
