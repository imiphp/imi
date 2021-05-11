<?php

namespace Imi\Rpc\Route;

use Imi\Rpc\Route\Annotation\Contract\IRpcController;
use Imi\Rpc\Route\Annotation\Contract\IRpcRoute;

interface IRoute
{
    /**
     * 增加路由规则，直接使用注解方式.
     *
     * @param \Imi\Rpc\Route\Annotation\Contract\IRpcController $controllerAnnotation
     * @param \Imi\Rpc\Route\Annotation\Contract\IRpcRoute      $routeAnnotation
     * @param mixed                                             $callable
     * @param array                                             $options
     *
     * @return void
     */
    public function addRuleAnnotation(IRpcController $controllerAnnotation, IRpcRoute $routeAnnotation, $callable, $options = []);

    /**
     * 获取缺省的路由注解.
     *
     * @param string                                            $className
     * @param string                                            $methodName
     * @param \Imi\Rpc\Route\Annotation\Contract\IRpcController $controllerAnnotation
     * @param array                                             $options
     *
     * @return \Imi\Rpc\Route\Annotation\Contract\IRpcRoute
     */
    public function getDefaultRouteAnnotation($className, $methodName, IRpcController $controllerAnnotation, $options = []);

    /**
     * 路由解析处理.
     *
     * @param mixed $data
     *
     * @return array
     */
    public function parse($data);
}
