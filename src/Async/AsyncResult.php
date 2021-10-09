<?php

declare(strict_types=1);

namespace Imi\Async;

use Imi\Async\Contract\IAsyncResult;

class AsyncResult implements IAsyncResult
{
    /**
     * @var mixed
     */
    private $result;

    /**
     * @param mixed $result
     */
    public function __construct($result = null)
    {
        $this->result = $result;
    }

    /**
     * 获取异步返回结果.
     *
     * 默认不超时无限等待，超时则会抛出异常
     *
     * @return mixed
     */
    public function get(?float $timeout = null)
    {
        $result = $this->result;
        if ($result instanceof IAsyncResult)
        {
            $result = $this->result->get($timeout);
            if ($result instanceof IAsyncResult)
            {
                $result = $result->get($timeout);
            }
        }

        return $result;
    }
}
