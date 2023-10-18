<?php

declare(strict_types=1);

namespace Imi\AMQP\Pool;

use Imi\App;
use Imi\Config;
use Imi\Pool\PoolManager;
use Imi\RequestContext;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Connection\AMQPConnectionConfig;
use PhpAmqpLib\Connection\AMQPConnectionFactory;

/**
 * AMQP 客户端连接池.
 */
class AMQPPool
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * 连接配置.
     */
    private static ?array $connections = null;

    /**
     * 获取新的连接实例.
     */
    public static function getNewInstance(?string $poolName = null): AbstractConnection
    {
        $poolName = static::parsePoolName($poolName);
        if (PoolManager::exists($poolName))
        {
            return PoolManager::getResource($poolName)->getInstance();
        }
        else
        {
            $config = Config::get('@app.amqp.connections.' . $poolName);
            if (null === $config)
            {
                throw new \RuntimeException(sprintf('Not found db config %s', $poolName));
            }

            $connection = self::createInstanceFromConfig($config);
            if (!$connection->isConnected())
            {
                throw new \RuntimeException(sprintf('AMQP %s connection failed', $poolName));
            }

            return $connection;
        }
    }

    /**
     * 获取连接实例，每个RequestContext中共用一个.
     */
    public static function getInstance(?string $poolName = null): ?AbstractConnection
    {
        $poolName = static::parsePoolName($poolName);
        if (PoolManager::exists($poolName))
        {
            return PoolManager::getRequestContextResource($poolName)->getInstance();
        }
        else
        {
            $requestContextKey = '__amqp.' . $poolName;
            $requestContext = RequestContext::getContext();
            if (isset($requestContext[$requestContextKey]))
            {
                return $requestContext[$requestContextKey];
            }
            if (null === self::$connections)
            {
                self::$connections = Config::get('@app.amqp.connections');
            }
            $config = self::$connections[$poolName] ?? null;
            if (null === $config)
            {
                throw new \RuntimeException(sprintf('Not found amqp config %s', $poolName));
            }
            /** @var AbstractConnection|null $connection */
            $connection = App::get($requestContextKey);
            if (null === $connection || !$connection->isConnected())
            {
                $connection = self::createInstanceFromConfig($config);
                if (!$connection->isConnected())
                {
                    throw new \RuntimeException(sprintf('AMQP %s connection failed', $poolName));
                }
                App::set($requestContextKey, $connection);
            }

            return $requestContext[$requestContextKey] = $connection;
        }
    }

    public static function createInstanceFromConfig(array $configArray): AbstractConnection
    {
        $config = new AMQPConnectionConfig();
        if (isset($configArray['ioType']))
        {
            $config->setIoType($configArray['ioType']);
        }
        if (isset($configArray['host']))
        {
            $config->setHost($configArray['host']);
        }
        if (isset($configArray['port']))
        {
            $config->setPort($configArray['port']);
        }
        if (isset($configArray['user']))
        {
            $config->setUser($configArray['user']);
        }
        if (isset($configArray['password']))
        {
            $config->setPassword($configArray['password']);
        }
        if (isset($configArray['vhost']))
        {
            $config->setVhost($configArray['vhost']);
        }
        if (isset($configArray['insist']))
        {
            $config->setInsist($configArray['insist']);
        }
        if (isset($configArray['loginMethod']))
        {
            $config->setLoginMethod($configArray['loginMethod']);
        }
        if (isset($configArray['loginResponse']))
        {
            $config->setLoginResponse($configArray['loginResponse']);
        }
        if (isset($configArray['locale']))
        {
            $config->setLocale($configArray['locale']);
        }
        if (isset($configArray['connectionTimeout']))
        {
            $config->setConnectionTimeout($configArray['connectionTimeout']);
        }
        if (isset($configArray['readTimeout']))
        {
            $config->setReadTimeout($configArray['readTimeout']);
        }
        if (isset($configArray['writeTimeout']))
        {
            $config->setWriteTimeout($configArray['writeTimeout']);
        }
        if (isset($configArray['channelRPCTimeout']))
        {
            $config->setChannelRpcTimeout($configArray['channelRPCTimeout']);
        }
        if (isset($configArray['heartbeat']))
        {
            $config->setHeartbeat($configArray['heartbeat']);
        }
        if (isset($configArray['keepalive']))
        {
            $config->setKeepalive($configArray['keepalive']);
        }
        if (isset($configArray['isSecure']))
        {
            $config->setIsSecure($configArray['isSecure']);
        }
        if (isset($configArray['networkProtocol']))
        {
            $config->setNetworkProtocol($configArray['networkProtocol']);
        }
        if (isset($configArray['streamContext']))
        {
            $config->setStreamContext($configArray['streamContext']);
        }
        if (isset($configArray['sendBufferSize']))
        {
            $config->setSendBufferSize($configArray['sendBufferSize']);
        }
        if (isset($configArray['dispatchSignals']))
        {
            $config->enableSignalDispatch($configArray['dispatchSignals']);
        }
        if (isset($configArray['amqpProtocol']))
        {
            $config->setAMQPProtocol($configArray['amqpProtocol']);
        }
        if (isset($configArray['protocolStrictFields']))
        {
            $config->setProtocolStrictFields($configArray['protocolStrictFields']);
        }
        if (isset($configArray['sslCaCert']))
        {
            $config->setSslCaCert($configArray['sslCaCert']);
        }
        if (isset($configArray['sslCaPath']))
        {
            $config->setSslCaPath($configArray['sslCaPath']);
        }
        if (isset($configArray['sslCert']))
        {
            $config->setSslCert($configArray['sslCert']);
        }
        if (isset($configArray['sslKey']))
        {
            $config->setSslKey($configArray['sslKey']);
        }
        if (isset($configArray['sslVerify']))
        {
            $config->setSslVerify($configArray['sslVerify']);
        }
        if (isset($configArray['sslVerifyName']))
        {
            $config->setSslVerifyName($configArray['sslVerifyName']);
        }
        if (isset($configArray['sslPassPhrase']))
        {
            $config->setSslPassphrase($configArray['sslPassPhrase']);
        }
        if (isset($configArray['sslCiphers']))
        {
            $config->setSslCiphers($configArray['sslCiphers']);
        }
        if (isset($configArray['sslSecurityLevel']))
        {
            $config->setSslSecurityLevel($configArray['sslSecurityLevel']);
        }
        if (isset($configArray['connectionName']))
        {
            $config->setConnectionName($configArray['connectionName']);
        }
        if (isset($configArray['debugPackets']))
        {
            $config->setDebugPackets($configArray['debugPackets']);
        }

        return AMQPConnectionFactory::create($config);
    }

    /**
     * 释放连接实例.
     */
    public static function release(AbstractConnection $client): void
    {
        $resource = RequestContext::get('poolResources.' . spl_object_id($client));
        if (null !== $resource)
        {
            PoolManager::releaseResource($resource);
        }
    }

    /**
     * 处理连接池名称.
     */
    public static function parsePoolName(?string $poolName = null): string
    {
        if (null === $poolName)
        {
            $poolName = static::getDefaultPoolName();
        }

        return $poolName;
    }

    /**
     * 获取默认池子名称.
     */
    public static function getDefaultPoolName(): string
    {
        // @phpstan-ignore-next-line
        return App::getBean('AMQP')->getDefaultPoolName();
    }
}
