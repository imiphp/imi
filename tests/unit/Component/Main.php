<?php
namespace Imi\Test\Component;

use Imi\Test\AppBaseMain;
use Imi\Util\File;
use Imi\Util\Imi;

class Main extends AppBaseMain
{
    public function __init()
    {
        // 这里可以做一些初始化操作，如果需要的话
        parent::__init();
        $path = Imi::getRuntimePath('test');
        if(is_dir($path))
        {
            File::deleteDir($path);
        }
    }

}