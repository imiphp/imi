<?php

namespace Imi\Rpc\Contract;

interface IRpcServer
{
    /**
     * 获取 RPC 类型.
     *
     * @return string
     */
    public function getRpcType(): string;

    /**
     * 获取控制器注解类.
     *
     * @return string
     */
    public function getControllerAnnotation(): string;

    /**
     * 获取动作注解类.
     *
     * @return string
     */
    public function getActionAnnotation(): string;

    /**
     * 获取路由注解类.
     *
     * @return string
     */
    public function getRouteAnnotation(): string;

    /**
     * 获取路由处理类.
     *
     * @return string
     */
    public function getRouteClass(): string;
}
