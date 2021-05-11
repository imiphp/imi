<?php

namespace Imi\ApiDoc;

use Imi\Main\BaseMain;
use Yurun\Doctrine\Common\Annotations\AnnotationReader;

class Main extends BaseMain
{
    public function __init()
    {
        AnnotationReader::addGlobalImports('oa', 'OpenApi\Annotations');
    }
}
