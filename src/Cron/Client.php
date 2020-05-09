<?php
namespace Imi\Cron;

use Imi\Cron\Message\IMessage;

class Client
{
    /**
     * socket 文件路径
     * 
     * 不支持 samba 文件共享
     *
     * @var string
     */
    private $socketFile;

    /**
     * socket 资源
     *
     * @var resource
     */
    private $socket;

    /**
     * 是否已连接
     *
     * @var boolean
     */
    private $connected;

    /**
     * 构造方法
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->options = $options;
        if(!isset($options['socketFile']))
        {
            throw new \InvalidArgumentException('If you want to use Swoole Shared Memory, you must set the "socketFile" option');
        }
        $this->socketFile = $options['socketFile'];
    }

    /**
     * 连接
     *
     * @return boolean
     */
    public function connect(): bool
    {
        if($this->connected)
        {
            return true;
        }
        $this->socket = $socket = stream_socket_client('unix://' . $this->socketFile, $errno, $errstr, 10);
        if(false === $socket)
        {
            $this->connected = false;
            return false;
        }
        stream_set_timeout($socket, 10);
        $this->connected = true;
        return true;
    }

    /**
     * 关闭连接
     *
     * @return void
     */
    public function close()
    {
        if($this->connected)
        {
            fclose($this->socket);
            $this->socket = null;
        }
    }

    /**
     * 是否已连接
     *
     * @return boolean
     */
    public function isConnected(): bool
    {
        return $this->connected;
    }

    /**
     * 发送操作
     *
     * @param IMessage $message
     * @return boolean
     */
    public function send(IMessage $message): bool
    {
        if(!$this->connected || !$this->connect())
        {
            return false;
        }
        $data = serialize($message);
        $length = strlen($data);
        $data = pack('N', $length) . $data;
        $length += 4;
        $result = fwrite($this->socket, $data, $length);
        if(false === $result)
        {
            $this->close();
        }
        return $length === $result;
    }

    /**
     * 接收结果
     *
     * @return \Imi\Cron\Message\IMessage|boolean
     */
    public function recv()
    {
        if(!$this->connected || !$this->connect())
        {
            return false;
        }
        $meta = fread($this->socket, 4);
        if('' === $meta || false === $meta)
        {
            $this->close();
            return false;
        }
        $length = unpack('N', $meta)[1];
        $data = fread($this->socket, $length);
        if(false === $data || !isset($data[$length - 1]))
        {
            $this->close();
            return false;
        }
        $result = unserialize($data);
        if($result instanceof IMessage)
        {
            return $result;
        }
        else
        {
            return false;
        }
    }
}