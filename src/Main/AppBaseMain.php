<?php

declare(strict_types=1);

namespace Imi\Main;

use Imi\Config;

/**
 * 项目主类基类.
 */
abstract class AppBaseMain extends BaseMain
{
    /**
     * 获取配置.
     */
    public function getConfig(): array
    {
        if (null === $this->config)
        {
            return $this->config = Config::get('@app');
        }

        return $this->config;
    }
}
