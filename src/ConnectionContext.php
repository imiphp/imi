<?php

declare(strict_types=1);

namespace Imi;

use Imi\Event\Event;
use Imi\Server\ConnectionContext\Event\Param\ConnectionContextRestoreParam;
use Imi\Server\ConnectionContext\StoreHandler;
use Imi\Server\ServerManager;

class ConnectionContext
{
    private function __construct()
    {
    }

    /**
     * 为当前连接创建上下文.
     */
    public static function create(array $data = [], ?string $serverName = null): void
    {
        $requestContextData = RequestContext::getContext();
        if (!static::get(null, null, null, $serverName) && $clientId = ($requestContextData['clientId'] ?? null))
        {
            static::use(static function (array $contextData) use ($data, $clientId, $requestContextData): array {
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
            }, $clientId, $serverName);
        }
    }

    /**
     * 从某个连接上下文中，加载到当前上下文或指定上下文中.
     */
    public static function load(int $fromClientId, ?int $toClientId = null, ?string $serverName = null): void
    {
        if (!$toClientId)
        {
            $toClientId = self::getClientId();
            if (null === $toClientId)
            {
                return;
            }
        }
        $data = static::getContext($fromClientId, $serverName);
        static::use(static function (array $contextData) use ($data, $toClientId): array {
            $contextData = $data;
            $contextData['clientId'] = $toClientId;

            return $contextData;
        }, $toClientId, $serverName);
    }

    /**
     * 销毁当前连接的上下文.
     *
     * @param int|string|null $clientId
     */
    public static function destroy($clientId = null, ?string $serverName = null): void
    {
        if (!$clientId)
        {
            $clientId = self::getClientId();
            if (null === $clientId)
            {
                return;
            }
        }
        /** @var \Imi\Server\ConnectionContext\StoreHandler $store */
        $store = self::getConnectionContextStore($serverName);
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
    public static function exists($clientId = null, ?string $serverName = null): bool
    {
        if (!$clientId)
        {
            $clientId = self::getClientId();
            if (null === $clientId)
            {
                return false;
            }
        }

        return self::getConnectionContextStore($serverName)->exists((string) $clientId);
    }

    /**
     * 获取上下文数据.
     *
     * @param mixed           $default
     * @param int|string|null $clientId
     *
     * @return mixed
     */
    public static function get(?string $name = null, $default = null, $clientId = null, ?string $serverName = null)
    {
        if (!$clientId)
        {
            $clientId = self::getClientId();
            if (null === $clientId)
            {
                return $default;
            }
        }
        $data = self::getConnectionContextStore($serverName)->read((string) $clientId);
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
    public static function set(?string $name, $value, $clientId = null, ?string $serverName = null): void
    {
        if (!$clientId)
        {
            $clientId = self::getClientId();
            if (null === $clientId)
            {
                return;
            }
        }
        $store = self::getConnectionContextStore($serverName);
        $clientIdStr = (string) $clientId;
        $result = $store->lock($clientIdStr, static function () use ($store, $name, $value, $clientIdStr) {
            $data = $store->read($clientIdStr);
            $data[$name] = $value;
            $store->save($clientIdStr, $data);
        });
        if (!$result)
        {
            throw new \RuntimeException('ConnectionContext lock fail');
        }
    }

    /**
     * 批量设置上下文数据.
     *
     * @param int|string|null $clientId
     */
    public static function muiltiSet(array $data, $clientId = null, ?string $serverName = null): void
    {
        if (!$clientId)
        {
            $clientId = self::getClientId();
            if (null === $clientId)
            {
                return;
            }
        }
        $store = self::getConnectionContextStore($serverName);
        $clientIdStr = (string) $clientId;
        $result = $store->lock($clientIdStr, static function () use ($store, $data, $clientIdStr) {
            $storeData = $store->read($clientIdStr);
            foreach ($data as $name => $value)
            {
                $storeData[$name] = $value;
            }
            $store->save($clientIdStr, $storeData);
        });
        if (!$result)
        {
            throw new \RuntimeException('ConnectionContext lock fail');
        }
    }

    /**
     * 使用回调并且自动加锁进行操作，回调用返回数据会保存进连接上下文.
     *
     * @param int|string|null $clientId
     */
    public static function use(callable $callable, $clientId = null, ?string $serverName = null): void
    {
        if (!$clientId)
        {
            $clientId = self::getClientId();
            if (null === $clientId)
            {
                return;
            }
        }
        /** @var \Imi\Server\ConnectionContext\StoreHandler $store */
        $store = self::getConnectionContextStore($serverName);
        $clientIdStr = (string) $clientId;
        $store->lock($clientIdStr, static function () use ($callable, $store, $clientIdStr) {
            $data = $store->read($clientIdStr);
            $result = $callable($data);
            if ($result)
            {
                $store->save($clientIdStr, $result);
            }
        });
    }

    /**
     * 获取一个闭包的值并将其持久化, 下次请求将直接从连接上下文中获取.
     *
     * @param int|string|null $clientId
     *
     * @return mixed
     */
    public static function remember(string $key, \Closure $closure, $clientId = null, ?string $serverName = null)
    {
        static::use(static function (array $ctx) use ($key, $closure, &$result) {
            if (isset($ctx[$key]))
            {
                $result = $ctx[$key];
                // no save
                return null;
            }
            else
            {
                $ctx[$key] = ($result = $closure());
                return $ctx;
            }
        }, $clientId, $serverName);

        return $result;
    }

    public static function unset(string $key): void
    {
        ConnectionContext::use(function (array $cxt) use ($key) {
            if (isset($cxt[$key]))
            {
                unset($cxt[$key]);

                return $cxt;
            }

            // no save
            return null;
        });
    }

    /**
     * 获取当前上下文.
     *
     * @param int|string|null $clientId
     */
    public static function getContext($clientId = null, ?string $serverName = null): array
    {
        return static::get(null, null, $clientId, $serverName);
    }

    /**
     * 绑定一个标记到当前连接.
     *
     * @param int|string|null $clientId
     */
    public static function bind(string $flag, $clientId = null, ?string $serverName = null): void
    {
        /** @var \Imi\Server\ConnectionContext\StoreHandler $store */
        $store = self::getConnectionContextStore($serverName);
        $store->bind($flag, $clientId ?? self::getClientId());
    }

    /**
     * 绑定一个标记到当前连接，如果已绑定返回false.
     *
     * @param int|string|null $clientId
     */
    public static function bindNx(string $flag, $clientId = null, ?string $serverName = null): bool
    {
        /** @var \Imi\Server\ConnectionContext\StoreHandler $store */
        $store = self::getConnectionContextStore($serverName);

        return $store->bindNx($flag, $clientId ?? self::getClientId());
    }

    /**
     * 取消绑定.
     *
     * @param int|string $clientId
     * @param int|null   $keepTime 旧数据保持时间，null 则不保留
     */
    public static function unbind(string $flag, $clientId, ?int $keepTime = null, ?string $serverName = null): void
    {
        /** @var \Imi\Server\ConnectionContext\StoreHandler $store */
        $store = self::getConnectionContextStore($serverName);
        $store->unbind($flag, $clientId, $keepTime);
    }

    /**
     * 使用标记获取连接编号.
     */
    public static function getClientIdByFlag(string $flag, ?string $serverName = null): array
    {
        /** @var \Imi\Server\ConnectionContext\StoreHandler $store */
        $store = self::getConnectionContextStore($serverName);

        return $store->getClientIdByFlag($flag);
    }

    /**
     * 使用标记获取连接编号.
     *
     * @param string[] $flags
     */
    public static function getClientIdsByFlags(array $flags, ?string $serverName = null): array
    {
        /** @var \Imi\Server\ConnectionContext\StoreHandler $store */
        $store = self::getConnectionContextStore($serverName);

        return $store->getClientIdsByFlags($flags);
    }

    /**
     * 使用连接编号获取标记.
     *
     * @param int|string $clientId
     */
    public static function getFlagByClientId($clientId, ?string $serverName = null): ?string
    {
        /** @var \Imi\Server\ConnectionContext\StoreHandler $store */
        $store = self::getConnectionContextStore($serverName);

        return $store->getFlagByClientId($clientId);
    }

    /**
     * 使用连接编号获取标记.
     *
     * @param int[]|string[] $clientIds
     *
     * @return string[]
     */
    public static function getFlagsByClientIds(array $clientIds, ?string $serverName = null): array
    {
        /** @var \Imi\Server\ConnectionContext\StoreHandler $store */
        $store = self::getConnectionContextStore($serverName);

        return $store->getFlagsByClientIds($clientIds);
    }

    /**
     * 使用标记获取旧的连接编号.
     */
    public static function getOldClientIdByFlag(string $flag, ?string $serverName = null): ?int
    {
        /** @var \Imi\Server\ConnectionContext\StoreHandler $store */
        $store = self::getConnectionContextStore($serverName);

        return $store->getOldClientIdByFlag($flag);
    }

    /**
     * 恢复标记对应连接中的数据.
     */
    public static function restore(string $flag, ?int $toClientId = null, ?string $serverName = null): void
    {
        $fromClientId = static::getOldClientIdByFlag($flag, $serverName);
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
        static::load($fromClientId, $toClientId, $serverName);
        static::bind($flag, $toClientId, $serverName);
        Event::trigger('IMI.CONNECT_CONTEXT.RESTORE', [
            'fromClientId' => $fromClientId,
            'toClientId'   => $toClientId,
            'serverName'   => $serverName,
        ], null, ConnectionContextRestoreParam::class);
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

    public static function getConnectionContextStore(?string $serverName = null): StoreHandler
    {
        return null === $serverName ? RequestContext::getServerBean('ConnectionContextStore') : ServerManager::getServer($serverName)->getBean('ConnectionContextStore');
    }
}
