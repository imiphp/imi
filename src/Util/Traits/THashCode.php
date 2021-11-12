<?php

declare(strict_types=1);

namespace Imi\Util\Traits;

trait THashCode
{
    /**
     * hashCode.
     */
    protected ?string $__hashCode = null;

    public function hashCode(): string
    {
        $hashCode = &$this->__hashCode;
        if (null === $hashCode)
        {
            $hashCode = spl_object_hash($this);
        }

        return $hashCode;
    }
}
