<?php

declare(strict_types=1);

namespace Imi\Model\Relation\Struct;

class PolymorphicOneToOne
{
    use TLeftAndRight;

    public function __construct(string $className, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicOneToOne $annotation)
    {
        $this->initLeftAndRight($className, $propertyName, $annotation);
    }
}
