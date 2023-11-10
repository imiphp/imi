<?php

declare(strict_types=1);

namespace Imi\Rpc\Route;

use Imi\Rpc\Route\Annotation\Contract\IRpcController;
use Imi\Rpc\Route\Annotation\Contract\IRpcRoute;

interface IRoute
{
    /**
     * 增加路由规则，直接使用注解方式.
     */
    public function addRuleAnnotation(IRpcController $controllerAnnotation, IRpcRoute $routeAnnotation, mixed $callable, array $options = []): void;

    /**
     * 获取缺省的路由注解.
     */
    public function getDefaultRouteAnnotation(string $className, string $methodName, IRpcController $controllerAnnotation, array $options = []): IRpcRoute;

    /**
     * 路由解析处理.
     */
    public function parse(mixed $data): array;
}
