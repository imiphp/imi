<?php

declare(strict_types=1);

namespace Imi\Fpm;

use Imi\App;
use Imi\Bean\BeanContexts;
use Imi\Bean\Scanner;
use Imi\Config;
use Imi\Core\App\Contract\BaseApp;
use Imi\Core\App\Enum\LoadRuntimeResult;
use Imi\Core\Runtime\Runtime;
use Imi\Fpm\Runtime\Handler\FpmRuntimeModeHandler;
use Imi\Fpm\Server\Type;
use Imi\Server\ServerManager;
use Imi\Util\File;
use Imi\Util\Imi;

class FpmApp extends BaseApp
{
    /**
     * 构造方法.
     */
    public function __construct(string $namespace)
    {
        parent::__construct($namespace);
        App::set(BeanContexts::FIXED_EVAL_NAME, true);
    }

    /**
     * 加载配置.
     */
    public function loadConfig(bool $initDotEnv = true): void
    {
        parent::loadConfig(false);
        $dir = Imi::getRuntimePath('classes');
        if (!is_dir($dir))
        {
            File::createDir($dir);
        }
        if ($initDotEnv)
        {
            $this->loadDotEnv();
        }

        $appConfig = Config::get('@app');
        $serverPath = $appConfig['fpm']['serverPath'] ?? null;
        if ($serverPath && ($fileName = File::path($serverPath, 'config/config.php')) && is_file($fileName))
        {
            Config::addConfig('@server.main', include $fileName);
        }
        else
        {
            Config::addConfig('@server.main', $appConfig);
        }
    }

    /**
     * 初始化运行时.
     */
    protected function initRuntime(): void
    {
        Runtime::setRuntimeModeHandler(FpmRuntimeModeHandler::class)->init();
    }

    /**
     * 加载运行时.
     */
    public function loadRuntime(): int
    {
        $this->initRuntime();
        // 尝试加载项目运行时
        $fileName = Imi::getRuntimePath('runtime');
        if (!Imi::loadRuntimeInfo($fileName))
        {
            $fileName = Imi::getRuntimePath('imi-runtime');
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
        $server->start();
    }

    /**
     * 获取应用类型.
     */
    public function getType(): string
    {
        return 'fpm';
    }
}
