<?php

declare(strict_types=1);

namespace Imi\Model\Relation\Struct;

class PolymorphicOneToMany
{
    use TLeftAndRight;

    public function __construct(string $className, string $propertyName, \Imi\Model\Annotation\Relation\PolymorphicOneToMany $annotation)
    {
        $this->initLeftAndRight($className, $propertyName, $annotation);
    }
}
