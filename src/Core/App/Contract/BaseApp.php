<?php

namespace Imi\Core\App\Contract;

use Imi\Config;
use Imi\Main\Helper;
use Imi\Util\Imi;

abstract class BaseApp implements IApp
{
    /**
     * 命名空间.
     *
     * @var string
     */
    protected string $namespace;

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
     * @return void
     */
    public function loadConfig(): void
    {
        // 加载框架配置
        Config::addConfig('@imi', include \dirname(IMI_PATH) . '/config/config.php');

        $paths = Imi::getNamespacePaths($this->namespace);
        // 加载项目目录下的 env
        \Dotenv\Dotenv::createImmutable($paths)->load();
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
    }
}
