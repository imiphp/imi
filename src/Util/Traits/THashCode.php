<?php

declare(strict_types=1);

namespace Imi\Util\Traits;

trait THashCode
{
    /**
     * hashCode.
     *
     * @var string
     */
    protected $hashCode;

    public function hashCode(): string
    {
        $hashCode = &$this->hashCode;
        if (null === $hashCode)
        {
            $hashCode = spl_object_hash($this);
        }

        return $hashCode;
    }
}
