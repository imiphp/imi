<?php

declare(strict_types=1);

namespace Imi\Workerman;

use Imi\Bean\Annotation;
use Imi\Bean\Scanner;
use Imi\Cli\CliApp;
use Imi\Config;
use Imi\Core\App\Enum\LoadRuntimeResult;
use Imi\Event\Event;
use Imi\Main\Helper;
use Imi\Util\Imi;
use Symfony\Component\Console\Input\ArgvInput;

class WorkermanApp extends CliApp
{
    /**
     * 构造方法.
     *
     * @param string $namespace
     *
     * @return void
     */
    public function __construct(string $namespace)
    {
        parent::__construct($namespace);
        Event::one('IMI.SCAN_APP', function () {
            $this->onScanApp();
        });
    }

    /**
     * 获取应用类型.
     *
     * @return string
     */
    public function getType(): string
    {
        return 'workerman';
    }

    /**
     * 加载配置.
     *
     * @param bool $initDotEnv
     *
     * @return void
     */
    public function loadConfig(bool $initDotEnv = true): void
    {
        parent::loadConfig(false);
        foreach (Config::get('@app.workermanServer', []) as $name => $config)
        {
            // 加载服务器配置文件
            foreach (Imi::getNamespacePaths($config['namespace']) as $path)
            {
                $fileName = $path . '/config/config.php';
                if (is_file($fileName))
                {
                    Config::addConfig('@server.' . $name, include $fileName);
                    break;
                }
            }
        }
        // @app.imi 配置检测
        if (null === Config::get('@app.imi.Timer'))
        {
            Config::set('@app.imi.Timer', WorkermanTimer::class);
        }
        if ($initDotEnv)
        {
            $this->loadDotEnv();
        }
    }

    /**
     * 加载运行时.
     *
     * @return int
     */
    public function loadRuntime(): int
    {
        $this->initRuntime();
        $input = new ArgvInput();
        $isServerStart = ('workerman/start' === ($_SERVER['argv'][1] ?? null));
        if ($isServerStart)
        {
            $result = false;
        }
        else
        {
            // 尝试加载项目运行时
            $appRuntimeFile = $input->getParameterOption('--app-runtime');
            if (false !== $appRuntimeFile && Imi::loadRuntimeInfo($appRuntimeFile))
            {
                return LoadRuntimeResult::ALL;
            }
        }
        // 尝试加载 imi 框架运行时
        if ($file = $input->getParameterOption('--imi-runtime'))
        {
            // 尝试加载指定 runtime
            $result = Imi::loadRuntimeInfo($file);
        }
        else
        {
            // 尝试加载默认 runtime
            $result = Imi::loadRuntimeInfo(Imi::getRuntimePath('imi-runtime'));
        }
        if ($result)
        {
            return LoadRuntimeResult::IMI_LOADED;
        }
        else
        {
            // 不使用缓存时去扫描
            Scanner::scanImi();
            if ($isServerStart)
            {
                $imiRuntime = Imi::getRuntimePath('imi-runtime-bak');
                Imi::buildRuntime($imiRuntime);
                // 执行命令行生成缓存
                $cmd = Imi::getImiCmd('imi/buildRuntime', [], [
                    'imi-runtime' => $imiRuntime,
                ]);
                do
                {
                    passthru(\Imi\cmd($cmd), $code);
                    $result = Imi::loadRuntimeInfo(Imi::getRuntimePath('runtime'));
                    sleep(1);
                } while (0 !== $code);

                return LoadRuntimeResult::ALL;
            }

            return LoadRuntimeResult::IMI_LOADED;
        }
    }

    /**
     * 加载入口.
     *
     * @return void
     */
    public function loadMain(): void
    {
        parent::loadMain();
        foreach (Config::get('@app.workermanServer', []) as $name => $config)
        {
            Helper::getMain($config['namespace'], 'server.' . $name);
        }
    }

    private function onScanApp(): void
    {
        $namespaces = [];
        foreach (Config::get('@app.workermanServer', []) as $config)
        {
            $namespaces[] = $config['namespace'];
        }
        Annotation::getInstance()->initByNamespace($namespaces);
    }
}
