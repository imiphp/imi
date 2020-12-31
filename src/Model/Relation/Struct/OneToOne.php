<?php

declare(strict_types=1);

namespace Imi\Model\Relation\Struct;

class OneToOne
{
    use TLeftAndRight;

    public function __construct(string $className, string $propertyName, \Imi\Model\Annotation\Relation\OneToOne $annotation)
    {
        $this->initLeftAndRight($className, $propertyName, $annotation);
    }
}
