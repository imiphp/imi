<?php

declare(strict_types=1);

namespace Imi\ApiDoc;

use Imi\Main\BaseMain;
use Yurun\Doctrine\Common\Annotations\AnnotationReader;

class Main extends BaseMain
{
    public function __init(): void
    {
        AnnotationReader::addGlobalImports('oa', 'OpenApi\Annotations');
    }
}
