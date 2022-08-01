<?php

declare(strict_types=1);

namespace Imi\Grpc\Util;

use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\Annotation\Bean;
use Imi\Bean\ReflectionContainer;
use Imi\Bean\ReflectionUtil;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Util\DocBlock;

/**
 * @Bean("GrpcInterfaceManager")
 */
class GrpcInterfaceManager
{
    /**
     * 接口集合.
     */
    private array $interfaces = [];

    public function __init(): void
    {
        foreach (AnnotationManager::getAnnotationPoints(Controller::class, 'class') as $point)
        {
            $refClass = ReflectionContainer::getClassReflection($point->getClass());
            foreach ($refClass->getInterfaces() as $interface)
            {
                if (preg_match('/Protobuf type <code>([^<]+)<\/code>/', $interface->getDocComment(), $matches))
                {
                    // 控制器是 Grpc
                    $serviceName = $matches[1];
                    $interfaceName = $interface->getName();
                    $methods = [];
                    $interfaceItem = ['serviceName' => $serviceName, 'methods' => &$methods];
                    foreach ($interface->getMethods() as $method)
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
                            $docblock = DocBlock::getDocBlock($docComment);
                            // @phpstan-ignore-next-line
                            $responseClass = (string) $docblock->getTagsByName('return')[0]->getType();
                        }

                        $methods[$method->getName()] = [
                            'request'  => $requestClass,
                            'response' => $responseClass,
                        ];
                    }
                    $this->interfaces[$interfaceName] = $interfaceItem;
                    break;
                }
            }
        }
    }

    /**
     * 获取请求类.
     */
    public function getRequest(string $interface, string $method): string
    {
        return $this->interfaces[$interface]['methods'][$method]['request'] ?? '';
    }

    /**
     * 获取响应类.
     */
    public function getResponse(string $interface, string $method): string
    {
        return $this->interfaces[$interface]['methods'][$method]['response'] ?? '';
    }

    /**
     * 获取服务名称.
     */
    public function getServiceName(string $interface): string
    {
        return $this->interfaces[$interface]['serviceName'] ?? '';
    }
}
