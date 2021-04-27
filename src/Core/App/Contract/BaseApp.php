<?php

declare(strict_types=1);

namespace Imi\Core\App\Contract;

use Imi\App;
use Imi\AppContexts;
use Imi\Config;
use Imi\Config\DotEnv\DotEnv;
use Imi\Core\Runtime\Handler\DefaultRuntimeModeHandler;
use Imi\Core\Runtime\Runtime;
use Imi\Event\Event;
use Imi\Main\Helper;
use Imi\Util\Imi;

abstract class BaseApp implements IApp
{
    /**
     * 命名空间.
     */
    protected string $namespace = '';

    /**
     * 应用模式的配置.
     */
    protected array $appConfig = [];

    /**
     * 构造方法.
     */
    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * 加载配置.
     */
    public function loadConfig(bool $initDotEnv = true): void
    {
        // 加载框架配置
        Config::addConfig('@imi', include \dirname(IMI_PATH) . '/config/config.php');

        $appPath = App::get(AppContexts::APP_PATH);
        $hasAppConfig = false;
        if ($appPath)
        {
            // 加载项目目录下的 env
            DotEnv::load([$appPath]);
            $fileName = $appPath . '/config/config.php';
            if (is_file($fileName))
            {
                Config::addConfig('@app', include $fileName);
                $hasAppConfig = true;
            }
        }
        else
        {
            $paths = Imi::getNamespacePaths($this->namespace);

            // 加载项目目录下的 env
            DotEnv::load($paths);

            // 加载项目配置文件
            foreach ($paths as $path)
            {
                $fileName = $path . '/config/config.php';
                if (is_file($fileName))
                {
                    Config::addConfig('@app', include $fileName);
                    $hasAppConfig = true;
                    break;
                }
            }
        }
        if (!$hasAppConfig)
        {
            Config::setConfig('@app', []);
        }

        App::setDebug(Config::get('@app.debug', true));
        if ($initDotEnv)
        {
            $this->loadDotEnv();
        }

        // 应用模式配置
        Config::set('@app.imi', array_merge($this->appConfig, Config::get('@app.imi', []), Config::get('@app.' . $this->getType() . '.imi', [])));
    }

    /**
     * 加载入口.
     */
    public function loadMain(): void
    {
        $this->initLogger();

        if (!Helper::getMain('Imi', 'imi'))
        {
            throw new \RuntimeException('Framework imi must have the class Imi\\Main');
        }

        Helper::getMain($this->namespace, 'app');
        Event::trigger('IMI.INIT_MAIN');
    }

    protected function loadDotEnv(): void
    {
        if ($_ENV)
        {
            // 加载 .env 配置
            foreach ($_ENV as $name => $value)
            {
                Config::set($name, $value);
            }
        }
    }

    /**
     * 初始化运行时.
     */
    protected function initRuntime(): void
    {
        Runtime::setRuntimeModeHandler(DefaultRuntimeModeHandler::class)->init();
    }

    /**
     * 初始化日志.
     */
    protected function initLogger(): void
    {
        $config = Config::get('@app.logger.channels.imi');
        if (null === $config)
        {
            Config::set('@app.logger.channels.imi', [
                'handlers' => [],
            ]);
        }
    }
}
