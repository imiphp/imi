<?php

declare(strict_types=1);

namespace Imi\Fpm;

use Imi\Core\App\Contract\BaseApp;

class FpmApp extends BaseApp
{
    /**
     * 获取应用类型.
     *
     * @return string
     */
    public function getType(): string
    {
        return 'fpm';
    }

    /**
     * 初始化.
     *
     * @return void
     */
    public function init(): void
    {
    }

    /**
     * 运行应用.
     *
     * @return void
     */
    public function run(): void
    {
    }
}
