<?php

declare(strict_types=1);

namespace
{
    use Imi\Env;
    use Imi\RequestContext;
    use Swoole\Coroutine;

    /**
     * 是否运行在 phar 模式.
     */
    \defined('IMI_IN_PHAR') || \define('IMI_IN_PHAR', (bool) \Phar::running(false));

    // @phpstan-ignore-next-line
    if (IMI_IN_PHAR)
    {
        /**
         * phar 构建时间的时间.
         *
         * 格式：2022-01-01T00:00:00+08:00
         */
        \defined('IMI_PHAR_BUILD_TIME') || \define('IMI_PHAR_BUILD_TIME', null);

        /**
         * phar 构建时的 Git commit hash.
         */
        \defined('IMI_PHAR_BUILD_GIT_HASH') || \define('IMI_PHAR_BUILD_GIT_HASH', null);
        /**
         * phar 构建时的 Git 分支.
         */
        \defined('IMI_PHAR_BUILD_GIT_BRANCH') || \define('IMI_PHAR_BUILD_GIT_BRANCH', null);

        /**
         * phar 构件时的 Git Tag.
         */
        \defined('IMI_PHAR_BUILD_GIT_TAG') || \define('IMI_PHAR_BUILD_GIT_TAG', null);
    }

    /**
     * 工作路径.
     */
    \defined('IMI_RUNNING_ROOT') || \define('IMI_RUNNING_ROOT', realpath(getcwd()));

    /**
     * 启动一个协程，自动创建和销毁上下文.
     *
     * @param mixed $args
     */
    function imigo(callable $callable, ...$args): int
    {
        $newCallable = imiCallable($callable);

        return Coroutine::create(static function (...$args) use ($newCallable) {
            $newCallable(...$args);
        }, ...$args);
    }

    /**
     * 为传入的回调自动创建和销毁上下文，并返回新的回调.
     *
     * @param bool $withGo 是否内置启动一个协程，如果为true，则无法获取回调返回值
     */
    function imiCallable(callable $callable, bool $withGo = false): callable
    {
        $server = RequestContext::get('server');
        $resultCallable = static function (...$args) use ($callable, $server) {
            RequestContext::set('server', $server);

            return $callable(...$args);
        };
        if ($withGo)
        {
            return static fn (...$args) => Coroutine::create(static fn (...$args) => $resultCallable(...$args), ...$args);
        }
        else
        {
            return $resultCallable;
        }
    }

    /**
     * 获取环境变量值
     *
     * @deprecated 3.0
     *
     * @param mixed $default
     *
     * @return mixed
     */
    function imiGetEnv(?string $varname = null, $default = null)
    {
        return Env::get($varname, $default);
    }
}

namespace Imi
{
    use Symfony\Component\Process\Process;

    /**
     * 处理命令行，执行后不会有 sh 进程.
     */
    function cmd(string $cmd): string
    {
        if ('Darwin' === \PHP_OS || 'Linux' === \PHP_OS)
        {
            return 'exec ' . $cmd;
        }
        else
        {
            return $cmd;
        }
    }

    /**
     * 尝试使用 tty 模式执行命令，可以保持带颜色格式的输出
     * 返回进程退出码
     *
     * @param string|array $commands
     */
    function ttyExec($commands, ?float $timeout = null, ?Process &$process = null): int
    {
        if (\is_array($commands))
        {
            $process = new Process($commands, null, null, null, $timeout);
        }
        else
        {
            $process = Process::fromShellCommandline($commands, null, null, null, $timeout);
        }

        if ('/' === \DIRECTORY_SEPARATOR && Process::isTtySupported())
        {
            $process->setTty(true);
            $process->run();
        }
        else
        {
            $process->run(static function ($type, $buffer) {
                echo $buffer;
            });
        }

        return $process->getExitCode();
    }

    /**
     * 获取环境变量值
     *
     * @param mixed $default
     *
     * @return mixed
     */
    function env(?string $varname = null, $default = null)
    {
        return Env::get($varname, $default);
    }
}
