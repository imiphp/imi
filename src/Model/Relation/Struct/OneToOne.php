<?php

namespace Imi\Model\Relation\Struct;

class OneToOne
{
    use TLeftAndRight;

    /**
     * @param string                                  $className
     * @param string                                  $propertyName
     * @param \Imi\Model\Annotation\Relation\OneToOne $annotation
     */
    public function __construct($className, $propertyName, $annotation)
    {
        $this->initLeftAndRight($className, $propertyName, $annotation);
    }
}
