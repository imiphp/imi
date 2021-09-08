<?php

declare(strict_types=1);

namespace Imi\Fpm;

use Imi\Bean\Scanner;
use Imi\Config;
use Imi\Core\App\Contract\BaseApp;
use Imi\Core\App\Enum\LoadRuntimeResult;
use Imi\Core\Runtime\Runtime;
use Imi\Event\Event;
use Imi\Fpm\Runtime\Handler\FpmRuntimeModeHandler;
use Imi\Fpm\Server\Type;
use Imi\Server\ServerManager;
use Imi\Util\File;
use Imi\Util\Imi;

class FpmApp extends BaseApp
{
    /**
     * 应用模式的配置.
     */
    protected array $appConfig = [
        'annotation_manager_annotations'               => false,
        'annotation_manager_annotations_cache'         => true,
        'annotation_manager_annotation_relation'       => false,
        'annotation_manager_annotation_relation_cache' => true,
    ];

    /**
     * 构造方法.
     */
    public function __construct(string $namespace)
    {
        parent::__construct($namespace);
    }

    protected function __loadConfig(): void
    {
        parent::__loadConfig();

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
        Event::trigger('IMI.APP.INIT', [], $this);
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
