<?php
namespace Imi\Util\Traits;

trait THashCode
{
    /**
     * hashCode
     *
     * @var string
     */
    protected $hashCode;

    public function hashCode(): string
    {
        if ($this->hashCode === null) {
            $this->hashCode = spl_object_hash($this);
        }

        return $this->hashCode;
    }
    
}