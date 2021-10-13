<?php

declare(strict_types=1);

namespace Imi\RoadRunner;

use Imi\Bean\Annotation;
use Imi\Bean\Scanner;
use Imi\Config;
use Imi\Core\App\Contract\BaseApp;
use Imi\Core\App\Enum\LoadRuntimeResult;
use Imi\Event\Event;
use Imi\Main\Helper;
use Imi\RoadRunner\Server\Type;
use Imi\Server\ServerManager;
use Imi\Util\Imi;

class RoadRunnerApp extends BaseApp
{
    /**
     * {@inheritDoc}
     */
    protected array $appConfig = [
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct(string $namespace)
    {
        parent::__construct($namespace);
        Event::one('IMI.SCAN_APP', function () {
            $this->onScanApp();
        });
    }

    /**
     * {@inheritDoc}
     */
    protected function __loadConfig(): void
    {
        parent::__loadConfig();

        $config = Config::get('@app.roadRunnerServer.main', []);
        // 加载服务器配置文件
        foreach (Imi::getNamespacePaths($config['namespace'] ?? $this->namespace) as $path)
        {
            $fileName = $path . '/config/config.php';
            if (is_file($fileName))
            {
                Config::addConfig('@server.main', include $fileName);
                break;
            }
        }
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function run(): void
    {
        $config = Config::get('@app.roadRunnerServer.main', []);
        $server = ServerManager::createServer('main', [
            'type'      => Type::HTTP,
            'namespace' => $config['namespace'] ?? $this->namespace,
        ]);
        Event::trigger('IMI.APP.INIT', [], $this);
        $server->start();
    }

    /**
     * {@inheritDoc}
     */
    public function getType(): string
    {
        return 'roadrunner';
    }

    /**
     * {@inheritDoc}
     */
    public function loadMain(): void
    {
        parent::loadMain();
        $config = Config::get('@app.roadRunnerServer.main', []);
        Helper::getMain($config['namespace'] ?? $this->namespace, 'server.main');
    }

    private function onScanApp(): void
    {
        $config = Config::get('@app.roadRunnerServer.main', []);
        $namespaces = [$config['namespace'] ?? $this->namespace];
        Annotation::getInstance()->initByNamespace($namespaces);
    }
}
