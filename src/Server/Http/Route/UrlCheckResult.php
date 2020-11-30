<?php

declare(strict_types=1);

namespace Imi\Server\Http\Route;

class UrlCheckResult
{
    /**
     * 结果.
     *
     * @var bool
     */
    public bool $result;

    /**
     * 参数.
     *
     * @var array
     */
    public array $params;

    /**
     * 结果忽略大小写.
     *
     * @var bool
     */
    public bool $resultIgnoreCase;

    public function __construct(bool $result, array $params = [], bool $resultIgnoreCase = false)
    {
        $this->result = $result;
        $this->params = $params;
        $this->resultIgnoreCase = $resultIgnoreCase;
    }
}
