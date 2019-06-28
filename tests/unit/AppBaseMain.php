<?php
namespace Imi\Test;

use Doctrine\Common\Annotations\AnnotationReader;

abstract class AppBaseMain extends \Imi\Main\AppBaseMain
{
    public function __init()
    {
        // 这里可以做一些初始化操作，如果需要的话
        
        AnnotationReader::addGlobalIgnoredName('testdox');
    }

}