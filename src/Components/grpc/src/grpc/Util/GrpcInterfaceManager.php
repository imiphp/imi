<?php

declare(strict_types=1);

namespace Imi\Grpc\Util;

use Imi\Bean\Annotation\Bean;
use Imi\Bean\ReflectionUtil;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionMethod;

/**
 * @Bean("GrpcInterfaceManager")
 */
class GrpcInterfaceManager
{
    /**
     * 接口集合.
     */
    private array $interfaces = [];

    /**
     * DocBlockFactory.
     */
    private DocBlockFactory $docBlockFactory;

    public function __construct()
    {
        $this->docBlockFactory = DocBlockFactory::createInstance();
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
     */
    public function initInterface(string $interface): void
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

            $docComment = $method->getDocComment();
            if (false === $docComment)
            {
                $responseClass = '';
            }
            else
            {
                $docblock = $this->docBlockFactory->create($docComment);
                // @phpstan-ignore-next-line
                $responseClass = (string) $docblock->getTagsByName('return')[0]->getType();
            }

            $data[$method->getName()] = [
                'request'   => $requestClass,
                'response'  => $responseClass,
            ];
        }
        $this->interfaces[$interface] = $data;
    }
}
