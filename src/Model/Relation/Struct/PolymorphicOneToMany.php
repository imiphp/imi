<?php

namespace Imi\Model\Relation\Struct;

class PolymorphicOneToMany
{
    use TLeftAndRight;

    public function __construct($className, $propertyName, $annotation)
    {
        $this->initLeftAndRight($className, $propertyName, $annotation);
    }
}
