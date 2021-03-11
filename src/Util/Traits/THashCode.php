<?php

namespace Imi\Util\Traits;

trait THashCode
{
    /**
     * hashCode.
     *
     * @var string|null
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
