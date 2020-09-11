<?php

namespace Imi\Server\Http\Route;

class UrlCheckResult
{
    /**
     * 结果.
     *
     * @var bool
     */
    public $result;

    /**
     * 参数.
     *
     * @var array
     */
    public $params;

    /**
     * 结果忽略大小写.
     *
     * @var bool
     */
    public $resultIgnoreCase;

    public function __construct($result, $params = [], $resultIgnoreCase = false)
    {
        $this->result = $result;
        $this->params = $params;
        $this->resultIgnoreCase = $resultIgnoreCase;
    }
}
