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
        $hashCode = &$this->hashCode;
        if ($hashCode === null) {
            $hashCode = spl_object_hash($this);
        }

        return $hashCode;
    }
    
}