<?php

declare(strict_types=1);

namespace Imi\RoadRunner\Server\Http;

use Imi\App;
use Imi\AppContexts;
use Imi\Bean\Annotation\Bean;
use Imi\Event\Event;
use Imi\Event\EventParam;
use Imi\RequestContext;
use Imi\RoadRunner\Http\Message\RoadRunnerResponse;
use Imi\RoadRunner\Util\RoadRunner;
use Imi\Server\Contract\BaseServer;
use Imi\Server\Http\Listener\HttpRouteInit;
use Imi\Server\Protocol;
use function Imi\ttyExec;
use Imi\Util\File;
use Imi\Util\Imi;
use Imi\Util\Socket\IPEndPoint;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

/**
 * @Bean("RoadRunnerHttpServer")
 */
class Server extends BaseServer
{
    protected \Spiral\RoadRunner\Worker $worker;

    protected \Spiral\RoadRunner\Http\PSR7Worker $psr7Worker;

    /**
     * 获取协议名称.
     */
    public function getProtocol(): string
    {
        return Protocol::HTTP;
    }

    /**
     * 是否为长连接服务
     */
    public function isLongConnection(): bool
    {
        return false;
    }

    /**
     * 是否支持 SSL.
     */
    public function isSSL(): bool
    {
        return $_SERVER['IMI_ROADRUNNER_SSL'] ?? false;
    }

    /**
     * 开启服务
     */
    public function start(): void
    {
        if (Imi::checkAppType('roadrunner'))
        {
            // worker
            Event::trigger('IMI.SERVER.WORKER_START', [
                'server'   => $this,
                'workerId' => 0,
            ], $this);
            try
            {
                // 初始化路由
                /** @var \Imi\Server\Http\Route\HttpRoute $route */
                $route = $this->getBean('HttpRoute');
                if ($route->isEmpty())
                {
                    (new HttpRouteInit())->handle(new EventParam(''));
                }
                /** @var \Imi\Server\Http\Dispatcher $dispatcher */
                $dispatcher = $this->getBean('HttpDispatcher');
                $this->worker = $worker = \Spiral\RoadRunner\Worker::create();
                $psrFactory = new \Imi\RoadRunner\Http\Psr17Factory();
                $this->psr7Worker = $worker = new \Spiral\RoadRunner\Http\PSR7Worker($worker, $psrFactory, $psrFactory, $psrFactory);

                while ($request = $worker->waitRequest())
                {
                    try
                    {
                        $response = new RoadRunnerResponse($worker);
                        RequestContext::muiltiSet([
                            'server'   => $this,
                            'request'  => $request,
                            'response' => $response,
                        ]);
                        // @phpstan-ignore-next-line
                        $dispatcher->dispatch($request);
                    }
                    catch (\Throwable $th)
                    {
                        if (true !== $this->getBean('HttpErrorHandler')->handle($th))
                        {
                            App::getBean('ErrorLog')->onException($th);
                        }
                        else
                        {
                            $worker->getWorker()->error((string) $th);
                        }
                    }
                }
            }
            finally
            {
                Event::trigger('IMI.SERVER.WORKER_STOP', [
                    'server'   => $this,
                    'workerId' => 0,
                ], $this);
            }
        }
        else
        {
            // 命令行启动
            [$cmd, $env] = (function () {
                $env = [];
                $cmd = escapeshellarg(RoadRunner::getBinaryPath()) . ' serve';
                $serverConfig = $this->config;
                $workDir = $serverConfig['workDir'] ?? null;
                if (null === $workDir)
                {
                    $workDir = App::get(AppContexts::APP_PATH);
                }
                if (null !== $workDir)
                {
                    $cmd .= ' -w ' . escapeshellarg($workDir);
                }
                $config = $serverConfig['config'] ?? null;
                if (null === $config && null !== $workDir)
                {
                    $config = File::path($workDir, '.rr.yaml');
                }
                if (null !== $config)
                {
                    $cmd .= ' -c ' . escapeshellarg($config);
                    $env['IMI_ROADRUNNER_CONFIG'] = $config;
                    $rrYaml = Yaml::parseFile($config);
                    $url = parse_url($rrYaml['http']['address'] ?? '');
                    if (false !== $url)
                    {
                        $env['IMI_ROADRUNNER_HOST'] = $url['host'];
                        $env['IMI_ROADRUNNER_PORT'] = $url['port'];
                    }
                    $env['IMI_ROADRUNNER_SSL'] = isset($rrYaml['http']['ssl']);
                }

                return [$cmd, $env];
            })();
            $process = Process::fromShellCommandline($cmd, null, $env, null, null);
            $isTTY = '/' === \DIRECTORY_SEPARATOR && Process::isTtySupported();
            if ($isTTY)
            {
                $process->setTty(true);
            }
            $process->start();
            File::putContents(Imi::getModeRuntimePath('roadrunner', 'server.pid'), (string) $process->getPid());
            if ($isTTY)
            {
                exit($process->wait());
            }
            else
            {
                exit($process->wait(function ($type, $buffer) {
                    echo $buffer;
                }));
            }
        }
    }

    /**
     * 终止服务
     */
    public function shutdown(): void
    {
        $fileName = Imi::getModeRuntimePath('roadrunner', 'server.pid');
        if (!is_file($fileName))
        {
            return;
        }
        $pid = (int) file_get_contents($fileName);
        if ('/' === \DIRECTORY_SEPARATOR)
        {
            if (\function_exists('posix_kill') && posix_kill($pid, \SIGTERM))
            {
                return;
            }
            $cmd = sprintf('kill -%d %d', \SIGTERM, $pid);
            if (Imi::checkAppType('roadrunner'))
            {
                exec($cmd, $output, $code);
            }
            else
            {
                $code = ttyExec($cmd);
            }
            if (0 === $code)
            {
                return;
            }
        }
        else
        {
            $cmd = sprintf('taskkill /t /f /pid %d', $pid);
            if (Imi::checkAppType('roadrunner'))
            {
                exec($cmd, $output, $code);
            }
            else
            {
                $code = ttyExec($cmd);
            }
            if (0 === $code)
            {
                return;
            }
        }
        throw new \RuntimeException(sprintf('Kill process %s failed', $pid));
    }

    /**
     * 重载服务
     */
    public function reload(): void
    {
        $cmd = escapeshellarg(RoadRunner::getBinaryPath()) . ' reset';
        $serverConfig = $this->config;
        $workDir = $serverConfig['workDir'] ?? null;
        if (null === $workDir)
        {
            $workDir = App::get(AppContexts::APP_PATH);
        }
        if (null !== $workDir)
        {
            $cmd .= ' -w ' . escapeshellarg($workDir);
        }
        $config = $serverConfig['config'] ?? null;
        if (null === $config && null !== $workDir)
        {
            $config = File::path($workDir, '.rr.yaml');
        }
        if (null !== $config)
        {
            $cmd .= ' -c ' . escapeshellarg($config);
        }
        if (Imi::checkAppType('roadrunner'))
        {
            exec($cmd, $output, $code);
        }
        else
        {
            $code = ttyExec($cmd);
        }
        if (0 !== $code)
        {
            throw new \RuntimeException(sprintf('Reload server failed! code: %s', $code));
        }
    }

    /**
     * 获取客户端地址
     *
     * @param string|int $clientId
     */
    public function getClientAddress($clientId): IPEndPoint
    {
        return new IPEndPoint($_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_PORT']);
    }

    public function getWorker(): \Spiral\RoadRunner\Worker
    {
        return $this->worker;
    }

    public function getPsr7Worker(): \Spiral\RoadRunner\Http\PSR7Worker
    {
        return $this->psr7Worker;
    }
}
