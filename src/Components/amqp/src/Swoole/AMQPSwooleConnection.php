<?php

declare(strict_types=1);

namespace Imi\AMQP\Swoole;

use Imi\Swoole\Util\Coroutine;
use PhpAmqpLib\Connection\AbstractConnection;

/**
 * @source https://github.com/swoole/php-amqplib/blob/master/PhpAmqpLib/Connection/AMQPSwooleConnection.php
 */
class AMQPSwooleConnection extends AbstractConnection
{
    /**
     * @param string   $host
     * @param int      $port
     * @param string   $user
     * @param string   $password
     * @param string   $vhost
     * @param bool     $insist
     * @param string   $login_method
     * @param null     $login_response      @deprecated
     * @param string   $locale
     * @param int      $connection_timeout
     * @param float    $read_write_timeout
     * @param resource $context
     * @param bool     $keepalive
     * @param int      $heartbeat
     * @param float    $channel_rpc_timeout
     */
    public function __construct(
        $host,
        $port,
        $user,
        $password,
        $vhost = '/',
        $insist = false,
        $login_method = 'AMQPLAIN',
        $login_response = null,
        $locale = 'en_US',
        $connection_timeout = 3,
        $read_write_timeout = 3.0,
        $context = null,
        $keepalive = false,
        $heartbeat = 0,
        $channel_rpc_timeout = 0.0
    ) {
        $io = new SwooleIO($host, $port, $connection_timeout, $read_write_timeout, $context, $keepalive, $heartbeat);

        parent::__construct(
            $user,
            $password,
            $vhost,
            $insist,
            $login_method,
            $login_response,
            $locale,
            $io,
            $heartbeat,
            $connection_timeout,
            $channel_rpc_timeout
        );
    }

    public function __destruct()
    {
        if (Coroutine::isIn())
        {
            parent::__destruct();
        }
        // @phpstan-ignore-next-line
        if ($this->io)
        {
            $this->io->close();
        }
    }
}
