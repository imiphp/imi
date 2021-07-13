<?php

declare(strict_types=1);

namespace Imi\Rpc\Route;

use Imi\Rpc\Route\Annotation\Contract\IRpcController;
use Imi\Rpc\Route\Annotation\Contract\IRpcRoute;

interface IRoute
{
    /**
     * 增加路由规则，直接使用注解方式.
     *
     * @param mixed $callable
     */
    public function addRuleAnnotation(IRpcController $controllerAnnotation, IRpcRoute $routeAnnotation, $callable, array $options = []): void;

    /**
     * 获取缺省的路由注解.
     *
     * @return \Imi\Rpc\Route\Annotation\Contract\IRpcRoute
     */
    public function getDefaultRouteAnnotation(string $className, string $methodName, IRpcController $controllerAnnotation, array $options = []);

    /**
     * 路由解析处理.
     *
     * @param mixed $data
     */
    public function parse($data): array;
}
