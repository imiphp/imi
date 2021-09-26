<?php

declare(strict_types=1);

namespace Imi\Cron;

use Imi\Cron\Message\IMessage;

class Client
{
    /**
     * 配置项.
     */
    private array $options = [];

    /**
     * socket 文件路径.
     *
     * 不支持 samba 文件共享
     */
    private string $socketFile = '';

    /**
     * socket 资源.
     *
     * @var resource|null
     */
    private $socket = null;

    /**
     * 是否已连接.
     */
    private bool $connected = false;

    /**
     * 构造方法.
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
        if (!isset($options['socketFile']))
        {
            throw new \InvalidArgumentException('You must set the "socketFile" option');
        }
        $this->socketFile = $options['socketFile'];
    }

    /**
     * 连接.
     */
    public function connect(): bool
    {
        if ($this->connected)
        {
            return true;
        }
        $this->socket = $socket = stream_socket_client('unix://' . $this->socketFile, $errno, $errstr, 10);
        if (false === $socket)
        {
            $this->connected = false;

            return false;
        }
        stream_set_timeout($socket, 10);
        $this->connected = true;

        return true;
    }

    /**
     * 关闭连接.
     */
    public function close(): void
    {
        if ($this->connected)
        {
            fclose($this->socket);
            $this->socket = null;
            $this->connected = false;
        }
    }

    /**
     * 是否已连接.
     */
    public function isConnected(): bool
    {
        return $this->connected;
    }

    /**
     * 发送操作.
     */
    public function send(IMessage $message): bool
    {
        if (!$this->connected || !$this->connect())
        {
            return false;
        }
        $data = serialize($message);
        $length = \strlen($data);
        $data = pack('N', $length) . $data;
        $length += 4;
        $result = fwrite($this->socket, $data, $length);
        if (false === $result)
        {
            $this->close();
        }

        return $length === $result;
    }

    /**
     * 接收结果.
     *
     * @return \Imi\Cron\Message\IMessage|bool
     */
    public function recv()
    {
        if (!$this->connected || !$this->connect())
        {
            return false;
        }
        $meta = fread($this->socket, 4);
        if ('' === $meta || false === $meta)
        {
            $this->close();

            return false;
        }
        $length = unpack('N', $meta)[1];
        $data = fread($this->socket, $length);
        if (false === $data || !isset($data[$length - 1]))
        {
            $this->close();

            return false;
        }
        $result = unserialize($data);
        if ($result instanceof IMessage)
        {
            return $result;
        }
        else
        {
            return false;
        }
    }
}
