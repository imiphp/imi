<?php

namespace Imi\Main;

use Imi\Config;

/**
 * 项目主类基类.
 */
abstract class AppBaseMain extends BaseMain
{
    /**
     * 加载配置.
     *
     * @return void
     */
    public function loadConfig()
    {
        parent::loadConfig();
        $configs = Config::get('@' . $this->moduleName . '.configs', []);
        if ($configs)
        {
            foreach ($configs as $name => $fileName)
            {
                Config::addConfig($name, include $fileName);
            }
        }
        // 在项目中配置 imi 启用哪些功能模块
        if ($beanScan = Config::get('@app.imi.beanScan'))
        {
            Config::set('@Imi.beanScan', $beanScan);
        }
    }
}
