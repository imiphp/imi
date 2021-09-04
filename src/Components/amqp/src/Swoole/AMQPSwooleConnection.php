<?php

namespace Imi\AMQP\Swoole;

use Imi\Util\Coroutine;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Wire\AMQPWriter;

/**
 * @source https://github.com/swoole/php-amqplib/blob/master/PhpAmqpLib/Connection/AMQPSwooleConnection.php
 */
class AMQPSwooleConnection extends AbstractConnection
{
    /**
     * @var int|null
     */
    protected $heartbeatTimerId = null;

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

    /**
     * Connects to the AMQP server.
     *
     * @return void
     */
    protected function connect()
    {
        parent::connect();
        $this->startHeartbeat();
    }

    /**
     * Requests a connection close.
     *
     * @param int    $reply_code
     * @param string $reply_text
     * @param array  $method_sig
     *
     * @return mixed|null
     */
    public function close($reply_code = 0, $reply_text = '', $method_sig = [0, 0])
    {
        $this->stopHeartbeat();
        $result = parent::close($reply_code, $reply_text, $method_sig);

        return $result;
    }

    protected function startHeartbeat(): void
    {
        if ($this->heartbeat > 0)
        {
            $this->heartbeatTimerId = \Swoole\Timer::tick($this->heartbeat * 500, function () {
                if ($this->isConnected())
                {
                    $this->write_heartbeat();
                }
            });
        }
    }

    protected function stopHeartbeat(): void
    {
        if ($this->heartbeatTimerId)
        {
            \Swoole\Timer::clear($this->heartbeatTimerId);
            $this->heartbeatTimerId = null;
        }
    }

    /**
     * Sends a heartbeat message.
     */
    protected function write_heartbeat(): void
    {
        $pkt = new AMQPWriter();
        $pkt->write_octet(8);
        $pkt->write_short(0);
        $pkt->write_long(0);
        $pkt->write_octet(0xCE);
        $this->write($pkt->getvalue());
    }
}
