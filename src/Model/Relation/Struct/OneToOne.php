<?php
namespace Imi\Model\Relation\Struct;

use Imi\Util\Imi;
use Imi\Util\Text;
use Imi\Model\ModelManager;

class OneToOne
{
    use TLeftAndRight;
    
    public function __construct($className, $propertyName, $annotation)
    {
        $this->initLeftAndRight($className, $propertyName, $annotation);
    }
}