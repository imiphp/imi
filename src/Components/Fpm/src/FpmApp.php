<?php

declare(strict_types=1);

namespace Imi\Fpm;

use Imi\Bean\Scanner;
use Imi\Config;
use Imi\Core\App\Contract\BaseApp;
use Imi\Core\App\Enum\LoadRuntimeResult;
use Imi\Fpm\Server\Type;
use Imi\Server\ServerManager;
use Imi\Util\Imi;

class FpmApp extends BaseApp
{
    /**
     * 加载运行时.
     *
     * @return int
     */
    public function loadRuntime(): int
    {
        // 尝试加载项目运行时
        $fileName = Imi::getRuntimePath('runtime.cache');
        if (!Imi::loadRuntimeInfo($fileName))
        {
            $fileName = Imi::getRuntimePath('imi-runtime.cache');
            $isBuildRuntime = !Imi::loadRuntimeInfo($fileName);
            if ($isBuildRuntime)
            {
                // 扫描 imi 框架
                Scanner::scanImi();
            }
            // 扫描组件
            Scanner::scanVendor();
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
     * 初始化.
     *
     * @return void
     */
    public function init(): void
    {
    }

    /**
     * 运行应用.
     *
     * @return void
     */
    public function run(): void
    {
        Config::addConfig('@server.main', Config::get('@app'));
        ServerManager::createServer('main', [
            'type'      => Type::HTTP,
            'namespace' => $this->namespace,
        ])->start();
    }

    /**
     * 获取应用类型.
     *
     * @return string
     */
    public function getType(): string
    {
        return 'fpm';
    }
}
