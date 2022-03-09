<?php

declare(strict_types=1);

namespace Imi\RoadRunner\Server\Http;

use Imi\App;
use Imi\AppContexts;
use Imi\Bean\Annotation\Bean;
use function Imi\cmd;
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
     * {@inheritDoc}
     */
    public function getProtocol(): string
    {
        return Protocol::HTTP;
    }

    /**
     * {@inheritDoc}
     */
    public function isLongConnection(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isSSL(): bool
    {
        return $_SERVER['IMI_ROADRUNNER_SSL'] ?? false;
    }

    /**
     * {@inheritDoc}
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
                RequestContext::set('server', $this);
                /** @var \Imi\Server\Http\Dispatcher $dispatcher */
                $dispatcher = $this->getBean('HttpDispatcher');
                $this->worker = $worker = \Spiral\RoadRunner\Worker::create();
                $psrFactory = new \Imi\RoadRunner\Http\Psr17Factory();
                $this->psr7Worker = $worker = new \Spiral\RoadRunner\Http\PSR7Worker($worker, $psrFactory, $psrFactory, $psrFactory);
                /** @var \Imi\Server\Http\Error\IErrorHandler $httpErrorHandler */
                $httpErrorHandler = $this->getBean('HttpErrorHandler');
                /** @var \Imi\Log\ErrorLog $errorLog */
                $errorLog = App::getBean('ErrorLog');

                $response = new RoadRunnerResponse($worker);
                while ($request = $worker->waitRequest())
                {
                    try
                    {
                        RequestContext::muiltiSet([
                            'request'  => $request,
                            'response' => $response,
                        ]);
                        // @phpstan-ignore-next-line
                        $dispatcher->dispatch($request);
                    }
                    catch (\Throwable $th)
                    {
                        if (true !== $httpErrorHandler->handle($th))
                        {
                            $errorLog->onException($th);
                        }
                        else
                        {
                            $worker->getWorker()->error((string) $th);
                        }
                    }
                    $response = new RoadRunnerResponse($worker);
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
            [$cmd, $env, $options] = (function () {
                $env = $options = [];
                $cmd = [
                    RoadRunner::getBinaryPath(),
                    'serve',
                ];
                $serverConfig = $this->config;
                $workDir = $serverConfig['workDir'] ?? App::get(AppContexts::APP_PATH_PHYSICS);
                if (null !== $workDir)
                {
                    $options['w'] = $workDir;
                    $cmd[] = '-w';
                    $cmd[] = $workDir;
                }
                $config = $serverConfig['config'] ?? null;
                if (null === $config && null !== $workDir)
                {
                    $config = File::path($workDir, '.rr.yaml');
                }
                if (null !== $config)
                {
                    $options['c'] = $config;
                    $cmd[] = '-c';
                    $cmd[] = $config;
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

                return [$cmd, $env, $options];
            })();
            $rrProcess = new Process($cmd, null, $env, null, null);
            try
            {
                if ('/' === \DIRECTORY_SEPARATOR && Process::isTtySupported())
                {
                    $rrProcess->setTty(true);
                }
            }
            catch (\Throwable $th)
            {
            }
            /** @var Process|null $hotUpdateProcess */
            $hotUpdateProcess = null;
            /** @var \Imi\RoadRunner\HotUpdate\HotUpdateProcess $hotUpdate */
            $hotUpdate = App::getBean('hotUpdate');
            // @phpstan-ignore-next-line
            if ($enableHotUpdate = ($hotUpdate->getStatus() && !IMI_IN_PHAR))
            {
                $hotUpdateCmd = cmd(Imi::getImiCmd('rr/hotUpdate', [], $options));
            }
            $rrProcess->start();
            try
            {
                File::putContents(Imi::getModeRuntimePath('roadrunner', 'server.pid'), (string) $rrProcess->getPid());
                while ($rrProcess->isRunning())
                {
                    // 热更新进程检测，没有运行就拉起
                    if ($enableHotUpdate && (!$hotUpdateProcess || !$hotUpdateProcess->isRunning()))
                    {
                        // @phpstan-ignore-next-line
                        $hotUpdateProcess = Process::fromShellCommandline($hotUpdateCmd, null, $env, null, null);
                        $hotUpdateProcess->start();
                    }
                    // RoadRunner worker 输出
                    echo $rrProcess->getIncrementalOutput(), $rrProcess->getIncrementalErrorOutput();
                    if ($hotUpdateProcess)
                    {
                        // 热更新进程输出
                        echo $hotUpdateProcess->getIncrementalOutput(), $hotUpdateProcess->getIncrementalErrorOutput();
                    }
                    usleep(1000);
                }
                echo $rrProcess->getIncrementalOutput(), $rrProcess->getIncrementalErrorOutput();
                // 停止热更新进程
                if ($hotUpdateProcess)
                {
                    $hotUpdateProcess->stop();
                    while ($hotUpdateProcess->isRunning())
                    {
                        echo $hotUpdateProcess->getIncrementalOutput(), $hotUpdateProcess->getIncrementalErrorOutput();
                        usleep(1000);
                    }
                    echo $hotUpdateProcess->getIncrementalOutput(), $hotUpdateProcess->getIncrementalErrorOutput();
                }
            }
            finally
            {
                if ($rrProcess->isRunning())
                {
                    $rrProcess->stop();
                }
                if ($hotUpdateProcess && $hotUpdateProcess->isRunning())
                {
                    $hotUpdateProcess->stop();
                }
            }
        }
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function reload(): void
    {
        $cmd = [
            RoadRunner::getBinaryPath(),
            'reset',
        ];
        $serverConfig = $this->config;
        $workDir = $serverConfig['workDir'] ?? null;
        if (null === $workDir)
        {
            $workDir = App::get(AppContexts::APP_PATH_PHYSICS);
        }
        if (null !== $workDir)
        {
            $cmd[] = '-w';
            $cmd[] = $workDir;
        }
        $config = $serverConfig['config'] ?? null;
        if (null === $config && null !== $workDir)
        {
            $config = File::path($workDir, '.rr.yaml');
        }
        if (null !== $config)
        {
            $cmd[] = '-c';
            $cmd[] = $config;
        }
        $process = new Process($cmd);
        $process->start();
        $process->waitUntil(static function ($type, $buffer) {
            if (Process::ERR === $type)
            {
                throw new \RuntimeException('Cannot reload RoadRunner: ' . $buffer);
            }

            return true;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getClientAddress($clientId): IPEndPoint
    {
        return new IPEndPoint($_SERVER['REMOTE_ADDR'], $_SERVER['REMOTE_PORT'] ?? 0);
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
