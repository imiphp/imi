<?php

declare(strict_types=1);

namespace Imi\Rpc\Client\Pool;

use Imi\Bean\BeanFactory;
use Imi\Pool\TUriResourceConfig;
use Imi\Swoole\Pool\BaseAsyncPool;

/**
 * Swoole协程RPC连接池.
 */
class RpcClientCoroutinePool extends BaseAsyncPool
{
    use TUriResourceConfig;

    /**
     * 资源类.
     *
     * @var string
     */
    protected $resource = RpcClientResource::class;

    public function __construct(string $name, \Imi\Pool\Interfaces\IPoolConfig $config = null, $resourceConfig = null)
    {
        parent::__construct($name, $config, $resourceConfig);
        $this->initUriResourceConfig();
    }

    /**
     * 创建资源.
     */
    protected function createResource(): \Imi\Pool\Interfaces\IPoolResource
    {
        $config = $this->getNextResourceConfig();

        return new $this->resource($this, BeanFactory::newInstance($config['clientClass'], $config));
    }
}
