<?php
/**
 * Created Wennlong Li
 * User: wenlong
 * Date: 2018/9/28
 * Time: 下午1:53
 */

namespace Imi\Resource;


trait
ResourceTrait
{
    protected $hashcode = null;

    public function hashcode() : string
    {
        if($this->hashcode === null) {
            $this->hashcode = spl_object_hash($this);
        }

        return $this->hashcode;
    }

    public function reset()
    {
        //todo:do reset resource flag
    }
}