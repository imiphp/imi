<?php

declare(strict_types=1);

namespace Imi\Pgsql\Test;

use Imi\Test\AppBaseMain;
use Yurun\Doctrine\Common\Annotations\AnnotationReader;

class Main extends AppBaseMain
{
    public function __init(): void
    {
        // 这里可以做一些初始化操作，如果需要的话
        parent::__init();
        AnnotationReader::addGlobalIgnoredName('depends');
    }
}
