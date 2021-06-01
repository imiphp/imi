<?php

declare(strict_types=1);

namespace Imi\Grpc\Util;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\ReflectionUtil;
use ReflectionClass;
use ReflectionMethod;

/**
 * @Bean("GrpcInterfaceManager")
 */
class GrpcInterfaceManager
{
    /**
     * 接口集合.
     *
     * @var array
     */
    private $interfaces = [];

    /**
     * DocBlockFactory.
     *
     * @var \phpDocumentor\Reflection\DocBlockFactory
     */
    private $docBlockFactory;

    public function __construct()
    {
        $this->docBlockFactory = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
    }

    /**
     * 获取请求类.
     */
    public function getRequest(string $interface, string $method): string
    {
        if (!isset($this->interfaces[$interface]))
        {
            $this->initInterface($interface);
        }

        return $this->interfaces[$interface][$method]['request'] ?? '';
    }

    /**
     * 获取响应类.
     */
    public function getResponse(string $interface, string $method): string
    {
        if (!isset($this->interfaces[$interface]))
        {
            $this->initInterface($interface);
        }

        return $this->interfaces[$interface][$method]['response'] ?? '';
    }

    /**
     * 初始化接口.
     *
     * @return void
     */
    public function initInterface(string $interface)
    {
        $refClass = new ReflectionClass($interface);
        $data = [];
        foreach ($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
        {
            $param = $method->getParameters()[0] ?? null;
            if (!$param || !($type = $param->getType()))
            {
                continue;
            }
            $requestClass = ReflectionUtil::getTypeCode($type, $refClass->getName());

            $docblock = $this->docBlockFactory->create($method->getDocComment());
            // @phpstan-ignore-next-line
            $responseClass = (string) $docblock->getTagsByName('return')[0]->getType();

            $data[$method->getName()] = [
                'request'   => $requestClass,
                'response'  => $responseClass,
            ];
        }
        $this->interfaces[$interface] = $data;
    }
}
