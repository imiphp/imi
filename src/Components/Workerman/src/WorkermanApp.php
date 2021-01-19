<?php

declare(strict_types=1);

namespace Imi\Workerman;

use Imi\Bean\Annotation;
use Imi\Cli\CliApp;
use Imi\Config;
use Imi\Event\Event;
use Imi\Main\Helper;
use Imi\Util\Imi;

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
     * @return void
     */
    public function loadConfig(): void
    {
        parent::loadConfig();
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
