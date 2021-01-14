<?php

declare(strict_types=1);

namespace Imi\Swoole\Test;

use Yurun\Doctrine\Common\Annotations\AnnotationReader;

abstract class AppBaseMain extends \Imi\Test\AppBaseMain
{
    public function __init()
    {
        // 这里可以做一些初始化操作，如果需要的话
        AnnotationReader::addGlobalIgnoredName('testdox');
    }
}
