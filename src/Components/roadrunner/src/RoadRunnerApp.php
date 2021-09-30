<?php

declare(strict_types=1);

namespace Imi\RoadRunner;

use Imi\Bean\Scanner;
use Imi\Config;
use Imi\Core\App\Contract\BaseApp;
use Imi\Core\App\Enum\LoadRuntimeResult;
use Imi\Event\Event;
use Imi\RoadRunner\Server\Type;
use Imi\Server\ServerManager;
use Imi\Util\Imi;

class RoadRunnerApp extends BaseApp
{
    /**
     * 应用模式的配置.
     */
    protected array $appConfig = [
    ];

    protected function __loadConfig(): void
    {
        parent::__loadConfig();

        foreach (Config::get('@app.roadRunnerServer', []) as $name => $config)
        {
            // 加载服务器配置文件
            foreach (Imi::getNamespacePaths($config['namespace'] ?? $this->namespace) as $path)
            {
                $fileName = $path . '/config/config.php';
                if (is_file($fileName))
                {
                    Config::addConfig('@server.' . $name, include $fileName);
                    break;
                }
            }
        }
    }

    /**
     * 加载运行时.
     */
    public function loadRuntime(): int
    {
        // 尝试加载项目运行时
        $fileName = Imi::getCurrentModeRuntimePath('runtime');
        if (!Imi::loadRuntimeInfo($fileName))
        {
            $fileName = Imi::getCurrentModeRuntimePath('imi-runtime');
            $isBuildRuntime = !Imi::loadRuntimeInfo($fileName, true);
            if ($isBuildRuntime)
            {
                // 扫描 imi 框架
                Scanner::scanImi();
                // 扫描组件
                Scanner::scanVendor();
            }
            if ($isBuildRuntime)
            {
                Imi::buildRuntime($fileName);
            }
            // 扫描项目
            Scanner::scanApp();
        }

        return LoadRuntimeResult::ALL;
    }

    /**
     * 运行应用.
     */
    public function run(): void
    {
        $server = ServerManager::getServer('main');
        if (null === $server)
        {
            $server = ServerManager::createServer('main', [
                'type'      => Type::HTTP,
                'namespace' => $this->namespace,
            ]);
        }
        Event::trigger('IMI.APP.INIT', [], $this);
        $server->start();
    }

    /**
     * 获取应用类型.
     */
    public function getType(): string
    {
        return 'roadrunner';
    }
}
