<?php

declare(strict_types=1);

namespace Imi\Server\Http\Route;

class UrlCheckResult
{
    /**
     * 结果.
     */
    public bool $result = false;

    /**
     * 参数.
     */
    public array $params = [];

    /**
     * 结果忽略大小写.
     */
    public bool $resultIgnoreCase = false;

    public function __construct(bool $result, array $params = [], bool $resultIgnoreCase = false)
    {
        $this->result = $result;
        $this->params = $params;
        $this->resultIgnoreCase = $resultIgnoreCase;
    }
}
