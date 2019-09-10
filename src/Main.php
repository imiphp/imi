<?php
namespace Imi;

use Imi\Main\BaseMain;
use Imi\Util\File;

/**
 * 主类
 */
class Main extends BaseMain
{
    public function __init()
    {
        require_once dirname(__DIR__) . '/config/listener.php';
    }

    /**
     * 加载配置
     * @return void
     */
    public function loadConfig()
    {
        $fileName = File::path(dirname($this->getPath()), 'config/config.php');
        if(is_file($fileName))
        {
            Config::addConfig('@' . $this->moduleName, include $fileName);
        }
    }
}