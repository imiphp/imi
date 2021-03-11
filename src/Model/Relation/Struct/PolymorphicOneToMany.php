<?php

namespace Imi\Model\Relation\Struct;

class PolymorphicOneToMany
{
    use TLeftAndRight;

    /**
     * @param string                                              $className
     * @param string                                              $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicOneToMany $annotation
     */
    public function __construct($className, $propertyName, $annotation)
    {
        $this->initLeftAndRight($className, $propertyName, $annotation);
    }
}
