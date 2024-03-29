<?php

declare(strict_types=1);

namespace Imi\Fpm;

use Imi\Bean\Scanner;
use Imi\Config;
use Imi\Core\App\Contract\BaseApp;
use Imi\Core\App\Enum\LoadRuntimeResult;
use Imi\Core\CoreEvents;
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
     * {@inheritDoc}
     */
    protected array $appConfig = [
        'annotation_manager_annotations'               => false,
        'annotation_manager_annotations_cache'         => true,
        'annotation_manager_annotation_relation'       => false,
        'annotation_manager_annotation_relation_cache' => true,
    ];

    /**
     * {@inheritDoc}
     */
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
     * {@inheritDoc}
     */
    protected function initRuntime(): void
    {
        Runtime::setRuntimeModeHandler(FpmRuntimeModeHandler::class)->init();
    }

    /**
     * {@inheritDoc}
     */
    public function loadRuntime(): int
    {
        $this->initRuntime();
        // 尝试加载项目运行时
        $fileName = Imi::getCurrentModeRuntimePath('runtime');
        if (!Imi::loadRuntimeInfo($fileName))
        {
            $fileName = Imi::getCurrentModeRuntimePath('imi-runtime');
            if (!Imi::loadRuntimeInfo($fileName, true))
            {
                // 扫描 imi 框架
                Scanner::scanImi(false);
                // 扫描组件
                Scanner::scanVendor(false);
                // 构建项目运行时缓存
                Imi::buildRuntime($fileName);
            }
            // 扫描项目
            Scanner::scanApp(false);
        }

        return LoadRuntimeResult::ALL;
    }

    /**
     * {@inheritDoc}
     */
    public function run(): void
    {
        $server = ServerManager::getServer('main');
        if (null === $server)
        {
            $server = ServerManager::createServer('main', Config::get('@app.fpmServer') + [
                'type'      => Type::HTTP,
                'namespace' => $this->namespace,
            ]);
        }
        Event::dispatch(eventName: CoreEvents::APP_INIT, target: $this);
        $server->start();
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): string
    {
        return 'fpm';
    }
}
