<?php

declare(strict_types=1);

namespace Imi\Swoole\Test\Component;

use Imi\Swoole\Test\AppBaseMain;
use Imi\Util\File;
use Imi\Util\Imi;

class Main extends AppBaseMain
{
    public function __init(): void
    {
        // 这里可以做一些初始化操作，如果需要的话
        parent::__init();
        $path = Imi::getRuntimePath('test');
        if (is_dir($path))
        {
            File::deleteDir($path);
        }
    }
}
