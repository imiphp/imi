<?php

declare(strict_types=1);

namespace Imi\Model\Relation\Struct;

class OneToMany
{
    use TLeftAndRight;

    public function __construct($className, $propertyName, $annotation)
    {
        $this->initLeftAndRight($className, $propertyName, $annotation);
    }
}
