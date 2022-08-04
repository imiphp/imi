<?php

declare(strict_types=1);

namespace Imi\Swoole\Process;

use Imi\App;
use Imi\AppContexts;
use Imi\Event\Event;
use Imi\Swoole\Process\Event\Param\PipeMessageEventParam;
use Imi\Swoole\Util\Coroutine;
use Swoole\Coroutine\Client;
use Swoole\Coroutine\Server;
use Swoole\Coroutine\Server\Connection;

trait TProcess
{
    protected bool $unixSocketRunning = false;

    protected ?Client $unixSocketClient = null;

    protected ?Server $unixSocketServer = null;

    /**
     * 发送消息.
     *
     * @return mixed
     */
    public function sendMessage(string $action, array $data = [])
    {
        $data['a'] = $action;
        $message = json_encode($data, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);

        return $this->write($message);
    }

    /**
     * @param int|null $exitCode
     */
    public function exit($exitCode = 0): void
    {
        if ($this->pid > 0)
        {
            parent::exit($exitCode);
        }
        else
        {
            exit($exitCode);
        }
    }

    public function getUnixSocketFile(): string
    {
        return '/tmp/imi.process.' . md5(App::get(AppContexts::APP_PATH)) . '.' . spl_object_id($this) . '.sock';
    }

    public function createUnixSocketClient(): Client
    {
        $client = new Client(\SWOOLE_SOCK_UNIX_STREAM);
        $client->set([
            'open_length_check'     => true,
            'package_length_type'   => 'N',
            'package_length_offset' => 0,
            'package_body_offset'   => 4,
        ]);
        if (!$client->connect($this->getUnixSocketFile()))
        {
            throw new \RuntimeException('Unable to connect to ' . $this->getUnixSocketFile() . ' (' . $client->errMsg . ')');
        }

        return $client;
    }

    public function getUnixSocketClient(): Client
    {
        if ($this->unixSocketClient)
        {
            return $this->unixSocketClient;
        }
        else
        {
            $this->unixSocketClient = $client = $this->createUnixSocketClient();
            Coroutine::create(function () use ($client) {
                while ($client->isConnected())
                {
                    $data = $client->recv(1);
                    if (false !== $data)
                    {
                        $data = swoole_substr_unserialize($data, 4);
                        Event::trigger('IMI.PROCESS.PIPE_MESSAGE', [
                            'process' => $this,
                            'action'  => $data['action'] ?? '',
                            'data'    => \is_array($data) && \array_key_exists('data', $data) ? $data['data'] : $data,
                        ], $this, PipeMessageEventParam::class);
                    }
                }
            });
        }

        return $client;
    }

    /**
     * 发送 UnixSocket 消息.
     *
     * @param mixed $data
     */
    public function sendUnixSocketMessage(string $action, $data = null): bool
    {
        $message = serialize([
            'action' => $action,
            'data'   => $data,
        ]);

        return $this->getUnixSocketClient()->send(pack('N', \strlen($message)) . $message) > 0;
    }

    /**
     * 发送 UnixSocket 消息.
     *
     * @param mixed $message
     */
    public function sendUnixSocketMessageRaw($message): bool
    {
        $message = serialize($message);

        return $this->getUnixSocketClient()->send(pack('N', \strlen($message)) . $message) > 0;
    }

    /**
     * 进程使用 IMI.PROCESS.PIPE_MESSAGE 事件返回的 Connection 对象发送消息.
     *
     * @param mixed $data
     */
    public function sendUnixSocketMessageByConnection(Connection $connection, string $action, $data = null): bool
    {
        $message = serialize([
            'action' => $action,
            'data'   => $data,
        ]);

        return $connection->send(pack('N', \strlen($message)) . $message) > 0;
    }

    public function startUnixSocketServer(): void
    {
        if ($this->unixSocketRunning || $this->pid !== getmypid())
        {
            return;
        }
        $this->unixSocketRunning = true;
        Event::on(['IMI.MAIN_SERVER.WORKER.EXIT', 'IMI.PROCESS.END'], function () {
            $this->stopUnixSocketServer();
        }, \Imi\Util\ImiPriority::IMI_MIN + 1);
        Coroutine::create(function () {
            $socketFile = $this->getUnixSocketFile();
            if (file_exists($socketFile))
            {
                unlink($socketFile);
            }
            $this->unixSocketServer = $server = new Server('unix:' . $socketFile);
            $server->set([
                'open_length_check'     => true,
                'package_length_type'   => 'N',
                'package_length_offset' => 0,
                'package_body_offset'   => 4,
            ]);
            // 接收到新的连接请求 并自动创建一个协程
            $server->handle(function (Connection $conn) {
                while ($this->unixSocketRunning)
                {
                    // 接收数据
                    $data = $conn->recv();

                    if ('' === $data || false === $data)
                    {
                        $conn->close();
                        break;
                    }

                    $data = swoole_substr_unserialize($data, 4);
                    Event::trigger('IMI.PROCESS.PIPE_MESSAGE', [
                        'process'    => $this,
                        'action'     => $data['action'] ?? '',
                        'data'       => \is_array($data) && \array_key_exists('data', $data) ? $data['data'] : $data,
                        'connection' => $conn,
                    ], $this, PipeMessageEventParam::class);
                }
            });

            // 开始监听端口
            $server->start();

            if (file_exists($socketFile))
            {
                unlink($socketFile);
            }
        });
    }

    public function stopUnixSocketServer(): void
    {
        $this->unixSocketRunning = false;
        if ($this->unixSocketServer)
        {
            $this->unixSocketServer->shutdown();
        }
    }
}

// @phpstan-ignore-next-line
if (\SWOOLE_VERSION_ID >= 50000)
{
    class Process extends \Swoole\Process
    {
        use TProcess {
            exit as private __exit;
        }

        public function exit(int $exitCode = 0): void
        {
            $this->__exit($exitCode);
        }
    }
}
else
{
    class Process extends \Swoole\Process
    {
        use TProcess;
    }
}
