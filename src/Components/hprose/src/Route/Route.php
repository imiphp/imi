<?php

declare(strict_types=1);

namespace Imi\Hprose\Route;

use Imi\Bean\Annotation\Bean;
use Imi\Hprose\Route\Annotation\HproseRoute;
use Imi\Rpc\Route\Annotation\Contract\IRpcController;
use Imi\Rpc\Route\Annotation\Contract\IRpcRoute;
use Imi\Rpc\Route\Annotation\RpcController;
use Imi\Rpc\Route\IRoute;
use Imi\Server\Route\RouteCallable;
use Imi\Server\ServerManager;
use Imi\Util\Text;

/**
 * @Bean("HproseRoute")
 */
class Route implements IRoute
{
    /**
     * 路由解析处理.
     *
     * @param mixed $data
     */
    public function parse($data): array
    {
        // 由 hprose 对象内部处理
        return [];
    }

    /**
     * 增加路由规则，直接使用注解方式.
     *
     * @param RpcController $controllerAnnotation
     * @param HproseRoute   $routeAnnotation
     * @param mixed         $callable
     */
    public function addRuleAnnotation(IRpcController $controllerAnnotation, IRpcRoute $routeAnnotation, $callable, array $options = []): void
    {
        // callable
        $callable = $this->parseCallable($callable);
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
     * 获取缺省的路由注解.
     */
    public function getDefaultRouteAnnotation(string $className, string $methodName, IRpcController $controllerAnnotation, array $options = []): HproseRoute
    {
        return new HproseRoute([
            'name'      => $methodName,
        ]);
    }

    /**
     * 处理回调.
     *
     * @param callable|RouteCallable $callable
     */
    private function parseCallable($callable): callable
    {
        if ($callable instanceof RouteCallable)
        {
            return $callable->getCallable();
        }
        else
        {
            return $callable;
        }
    }
}
