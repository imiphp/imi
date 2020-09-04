<?php
namespace Imi\Fpm;

use Imi\Core\Contract\BaseApp;

class FpmApp extends BaseApp
{
    /**
     * 获取应用类型
     *
     * @return string
     */
    public function getType(): string
    {
        return 'fpm';
    }

    /**
     * 运行应用
     *
     * @return void
     */
    public function run(): void
    {
    }

}
