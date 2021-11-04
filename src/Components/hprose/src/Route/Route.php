<?php

declare(strict_types=1);

namespace Imi\Hprose\Route;

use Imi\Bean\Annotation\Bean;
use Imi\Hprose\Route\Annotation\HproseRoute;
use Imi\Rpc\Route\Annotation\Contract\IRpcController;
use Imi\Rpc\Route\Annotation\Contract\IRpcRoute;
use Imi\Rpc\Route\Annotation\RpcController;
use Imi\Rpc\Route\IRoute;
use Imi\Server\ServerManager;
use Imi\Util\Text;

/**
 * @Bean("HproseRoute")
 */
class Route implements IRoute
{
    /**
     * {@inheritDoc}
     */
    public function parse($data): array
    {
        // 由 hprose 对象内部处理
        return [];
    }

    /**
     * {@inheritDoc}
     *
     * @param RpcController $controllerAnnotation
     * @param HproseRoute   $routeAnnotation
     */
    public function addRuleAnnotation(IRpcController $controllerAnnotation, IRpcRoute $routeAnnotation, $callable, array $options = []): void
    {
        // callable
        $isObject = \is_array($callable) && isset($callable[0]) && $callable[0] instanceof IRpcController;
        if ($isObject)
        {
            // 复制一份控制器对象
            $callable[0] = clone $callable[0];
        }

        $serverName = $options['serverName'];
        /** @var \Hprose\Swoole\Socket\Service $hproseServer */
        // @phpstan-ignore-next-line
        $hproseServer = ServerManager::getServer($serverName)->getHproseService();

        // alias
        if (Text::isEmpty($controllerAnnotation->prefix))
        {
            $alias = $routeAnnotation->name;
        }
        // @phpstan-ignore-next-line
        elseif (\is_string($routeAnnotation->name))
        {
            $alias = $controllerAnnotation->prefix . $routeAnnotation->name;
        }
        else
        {
            throw new \RuntimeException('Invalid route');
        }

        // funcOptions
        $funcOptions = [
            'mode'          => $routeAnnotation->mode,
            'simple'        => $routeAnnotation->simple,
            'oneway'        => $routeAnnotation->oneway,
            'async'         => $routeAnnotation->async,
            'passContext'   => $routeAnnotation->passContext,
        ];

        $hproseServer->addFunction($callable, $alias, $funcOptions);
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultRouteAnnotation(string $className, string $methodName, IRpcController $controllerAnnotation, array $options = []): HproseRoute
    {
        return new HproseRoute([
            'name'      => $methodName,
        ]);
    }
}
