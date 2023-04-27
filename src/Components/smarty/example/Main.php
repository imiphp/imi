<?php

declare(strict_types=1);

namespace Imi\Smarty\Example;

use Imi\Main\AppBaseMain;
use Yurun\Doctrine\Common\Annotations\AnnotationReader;

class Main extends AppBaseMain
{
    public function __init(): void
    {
        // 这里可以做一些初始化操作，如果需要的话
        AnnotationReader::addGlobalIgnoredName('testdox');
    }
}
