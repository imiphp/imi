<?php

namespace Imi\Model\Relation\Struct;

class PolymorphicOneToOne
{
    use TLeftAndRight;

    /**
     * @param string                                             $className
     * @param string                                             $propertyName
     * @param \Imi\Model\Annotation\Relation\PolymorphicOneToOne $annotation
     */
    public function __construct($className, $propertyName, $annotation)
    {
        $this->initLeftAndRight($className, $propertyName, $annotation);
    }
}
