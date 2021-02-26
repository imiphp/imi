<?php

declare(strict_types=1);

namespace Imi\Core\App\Contract;

use Imi\App;
use Imi\AppContexts;
use Imi\Config;
use Imi\Config\Dotenv\DotEnv;
use Imi\Core\Runtime\Handler\DefaultRuntimeModeHandler;
use Imi\Core\Runtime\Runtime;
use Imi\Event\Event;
use Imi\Main\Helper;
use Imi\Util\Imi;

abstract class BaseApp implements IApp
{
    /**
     * 命名空间.
     *
     * @var string
     */
    protected string $namespace = '';

    /**
     * 构造方法.
     *
     * @param string $namespace
     *
     * @return void
     */
    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
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
        // 加载框架配置
        Config::addConfig('@imi', include \dirname(IMI_PATH) . '/config/config.php');

        $appPath = App::get(AppContexts::APP_PATH);
        if ($appPath)
        {
            // 加载项目目录下的 env
            DotEnv::load([$appPath]);
            $fileName = $appPath . '/config/config.php';
            if (is_file($fileName))
            {
                Config::addConfig('@app', include $fileName);
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
                    break;
                }
            }
        }

        App::setDebug(Config::get('@app.debug', true));
        if ($initDotEnv)
        {
            $this->loadDotEnv();
        }
    }

    /**
     * 加载入口.
     *
     * @return void
     */
    public function loadMain(): void
    {
        if (!Helper::getMain('Imi', 'imi'))
        {
            throw new \RuntimeException('Framework imi must have the class Imi\\Main');
        }
        Helper::getMain($this->namespace, 'app');
        Event::trigger('IMI.INIT_MAIN');
    }

    protected function loadDotEnv(): void
    {
        // 加载 .env 配置
        foreach ($_ENV as $name => $value)
        {
            Config::set($name, $value);
        }
    }

    /**
     * 初始化运行时.
     *
     * @return void
     */
    protected function initRuntime()
    {
        Runtime::setRuntimeModeHandler(DefaultRuntimeModeHandler::class)->init();
    }
}
