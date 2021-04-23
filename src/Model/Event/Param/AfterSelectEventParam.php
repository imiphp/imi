<?php

namespace Imi\Model\Event\Param;

use Imi\Event\EventParam;

class AfterSelectEventParam extends EventParam
{
    /**
     * 查询结果.
     *
     * @var \Imi\Model\BaseModel[]
     */
    public $result;

    /**
     * @param string      $eventName
     * @param array       $data
     * @param object|null $target
     */
    public function __construct($eventName, $data = [], $target = null)
    {
        $this->__eventName = $eventName;
        $this->__target = $target;
        $this->__data = $data;
        if ($data)
        {
            foreach ($data as $key => $value)
            {
                $this->$key = &$value;
            }
        }
    }
}
