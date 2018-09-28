<?php
namespace Imi\Pool;

use Imi\Pool\Interfaces\IPool;
use Imi\Pool\Interfaces\IPoolResource;
use Imi\Resource\ResourceTrait;

abstract class BasePoolResource implements IPoolResource
{
    use ResourceTrait;
    /**
     * 池子实例
     * @var IPool
     */
    private $pool;

    public function __construct(IPool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * 获取池子实例
     * @return IPool
     */
    public function getPool(): IPool
    {
        return $this->pool;
    }

}