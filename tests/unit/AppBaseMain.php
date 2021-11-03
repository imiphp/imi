<?php

declare(strict_types=1);

namespace Imi\Test;

use Yurun\Doctrine\Common\Annotations\AnnotationReader;

abstract class AppBaseMain extends \Imi\Main\AppBaseMain
{
    public function __init(): void
    {
        // 这里可以做一些初始化操作，如果需要的话
        AnnotationReader::addGlobalIgnoredName('testdox');
        AnnotationReader::addGlobalIgnoredName('dataProvider');
    }
}
