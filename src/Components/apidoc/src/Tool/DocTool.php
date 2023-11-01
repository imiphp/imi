<?php

declare(strict_types=1);

namespace Imi\ApiDoc\Tool;

use Imi\App;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\ReflectionUtil;
use Imi\Cli\Annotation\Argument;
use Imi\Cli\Annotation\Command;
use Imi\Cli\Annotation\CommandAction;
use Imi\Cli\Annotation\Option;
use Imi\Cli\Contract\BaseCommand;
use Imi\Log\Logger;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Util\ClassObject;
use Imi\Util\DocBlock;
use OpenApi\Analysis;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\MediaType;
use OpenApi\Annotations\Operation as AnnotationsOperation;
use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\RequestBody;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Schema;
use OpenApi\Context;
use OpenApi\Generator;

#[Command(name: 'doc')]
class DocTool extends BaseCommand
{
    /**
     * 生成 API 接口文档.
     */
    #[CommandAction(name: 'api')]
    #[Argument(name: 'to', type: \Imi\Cli\ArgType::STRING, required: true, comments: '生成到的目标文件名')]
    #[Option(name: 'namespace', type: \Imi\Cli\ArgType::STRING, comments: '指定扫描的命名空间，多个用半角逗号分隔')]
    public function api(string $to, ?string $namespace): void
    {
        $directory = $controllerClasses = [];
        // 处理要扫描的目录/文件
        $controllerAnnotationPoints = AnnotationManager::getAnnotationPoints(Controller::class, 'class');
        if ($namespace)
        {
            // 指定命名空间
            foreach (explode(',', $namespace) as $ns)
            {
                foreach ($controllerAnnotationPoints as $point)
                {
                    $class = $point->getClass();
                    if (ClassObject::inNamespace($ns, $class))
                    {
                        $controllerClasses[] = $class;
                        $ref = new \ReflectionClass($class);
                        $directory[] = $ref->getFileName();
                    }
                }
            }
        }
        else
        {
            // 扫描全部命名空间
            foreach ($controllerAnnotationPoints as $point)
            {
                $class = $point->getClass();
                $controllerClasses[] = $class;
                $ref = new \ReflectionClass($class);
                $directory[] = $ref->getFileName();
            }
        }
        if (!$directory)
        {
            echo 'Api route not found!', \PHP_EOL;

            return;
        }
        // 生成
        /** @var Logger $loggerInstance */
        $loggerInstance = App::getBean('Logger');
        $generator = new Generator($loggerInstance->getLogger());

        $processors = $generator->getProcessors();
        array_unshift($processors, function (Analysis $analysis) use ($controllerClasses): void {
            $this->parseRoute($analysis, $controllerClasses);
        });

        $openapi = $generator
            ->setProcessors($processors)
            ->generate([$directory]);
        $openapi->saveAs($to);
    }

    /**
     * 处理路由.
     */
    private function parseRoute(Analysis $analysis, array $controllerClasses): void
    {
        // OpenApi 扫描
        $map = [];
        $info = null;
        foreach ($analysis->annotations as $annotation)
        {
            if (!isset($annotation->_context))
            {
                continue;
            }
            /** @var \OpenApi\Context $context */
            $context = $annotation->_context;
            /** @var \OpenApi\Annotations\AbstractAnnotation $annotation */
            $className = $context->namespace . '\\' . $context->class;
            $map[$className][$context->method][$annotation::class][] = $annotation;
            if ($annotation instanceof Info)
            {
                $info = $annotation;
            }
        }
        if (!$info)
        {
            $context = new Context();
            $infoAnnotation = new Info([
                'title'     => App::getNamespace(),
                'version'   => '1.0.0',
                '_context'  => $context,
            ]);
            $analysis->addAnnotation($infoAnnotation, $context);
        }
        // 遍历 imi 控制器类
        foreach ($controllerClasses as $controllerClass)
        {
            // 控制器注解
            /** @var Controller|null $controllerAnnotation */
            $controllerAnnotation = AnnotationManager::getClassAnnotations($controllerClass, Controller::class, true, true);
            if (!$controllerAnnotation)
            {
                continue;
            }
            $refClass = new \ReflectionClass($controllerClass);
            // 动作注解
            $actionPointMaps = AnnotationManager::getMethodsAnnotations($controllerClass, Action::class);
            foreach ($actionPointMaps as $method => $_)
            {
                $actionMapItem = $map[$controllerClass][$method] ?? [];
                $hasOperation = false;
                foreach ($actionMapItem as $annotationClass => $__)
                {
                    if ($hasOperation = is_subclass_of($annotationClass, AnnotationsOperation::class))
                    {
                        break;
                    }
                }
                $refMethod = new \ReflectionMethod($controllerClass, $method);
                if (!$hasOperation)
                {
                    // 自动增加个请求
                    /** @var Route $route */
                    $route = AnnotationManager::getMethodAnnotations($controllerClass, $method, Route::class, true, true);

                    // path
                    $requestPath = $route->url ?? $method;
                    if ('/' !== ($requestPath[0] ?? null))
                    {
                        $requestPath = $controllerAnnotation->prefix . $requestPath;
                    }

                    $comment = $refMethod->getDocComment();
                    if (false === $comment)
                    {
                        $comment = '';
                        $docParams = [];
                    }
                    else
                    {
                        $docblock = DocBlock::getDocBlock($comment);
                        /** @var \phpDocumentor\Reflection\DocBlock\Tags\Param[] $docParams */
                        $docParams = $docblock->getTagsByName('param');
                    }

                    // method
                    $requestMethods = (array) ($route->method ?? 'GET');
                    $hasGet = false;
                    foreach ($requestMethods as $requestMethod)
                    {
                        if ('get' === strtolower($requestMethod))
                        {
                            $hasGet = true;
                            break;
                        }
                    }

                    // parameters
                    $requestParameters = [];
                    $requestBody = null;
                    if ($hasGet)
                    {
                        foreach ($refMethod->getParameters() as $param)
                        {
                            $docParam = $this->getDocParam($docParams, $param->getName());

                            $requestParameters[] = new Parameter([
                                'parameter'     => $controllerClass . '::' . $method . '@request.' . $param->getName(),
                                'name'          => $param->getName(),
                                'in'            => 'query',
                                'required'      => !$param->isOptional(),
                                'description'   => $docParam ? (string) $docParam->getDescription() : Generator::UNDEFINED,
                                '_context'      => $context ?? null,
                            ]);
                        }
                    }
                    else
                    {
                        $properties = $requiredProperties = [];
                        foreach ($refMethod->getParameters() as $param)
                        {
                            if (!$param->isDefaultValueAvailable())
                            {
                                $requiredProperties[] = $param->getName();
                            }
                            $docParam = $this->getDocParam($docParams, $param->getName());
                            $typeText = ReflectionUtil::getTypeCode($param->getType(), $refMethod->getDeclaringClass()->getName());
                            if ('array' === $typeText)
                            {
                                $type = $param->getType();
                                if ($type && ReflectionUtil::allowsType($type, 'array'))
                                {
                                    if ($docParam && $type = $docParam->getType())
                                    {
                                        $type = $type->__toString();
                                        $types = explode('|', $type);
                                        foreach ($types as &$type)
                                        {
                                            if (str_ends_with($type, '[]'))
                                            {
                                                $type = substr($type, 0, -2);
                                                break;
                                            }
                                        }
                                        unset($type);
                                        $type = implode('|', $types);
                                        $items = new Items([
                                            'type' => $type,
                                        ]);
                                    }
                                    else
                                    {
                                        $items = new Items([
                                            'type' => 'mixed',
                                        ]);
                                    }
                                }
                                else
                                {
                                    $items = Generator::UNDEFINED;
                                }
                            }
                            else
                            {
                                $items = Generator::UNDEFINED;
                            }
                            $properties[] = new Property([
                                'property'  => $param->getName(),
                                'type'      => $typeText,
                                'title'     => $docParam ? (string) $docParam->getDescription() : Generator::UNDEFINED,
                                '_context'  => $context ?? null,
                                'items'     => $items,
                            ]);
                        }
                        $schema = new Schema([
                            'schema'     => $controllerClass . '::' . $method . '@request',
                            'title'      => $controllerClass . '::' . $method . '@request',
                            'type'       => 'object',
                            'properties' => $properties,
                            'required'   => $requiredProperties,
                            '_context'   => $context ?? null,
                        ]);
                        $requestContent = new MediaType([
                            'mediaType' => 'application/json',
                            'schema'    => $schema,
                            '_context'  => $context ?? null,
                        ]);
                        $requestBody = new RequestBody([
                            'request'   => $controllerClass . '::' . $method . '@request',
                            'content'   => [
                                $requestContent,
                            ],
                            '_context'  => $context ?? null,
                        ]);
                    }

                    $methodContext = new Context([
                        'comment'   => $comment,
                        'filename'  => $refMethod->getFileName(),
                        'line'      => $refMethod->getStartLine(),
                        'namespace' => $refClass->getNamespaceName(),
                        'class'     => $refClass->getShortName(),
                        'method'    => $method,
                    ]);

                    $defaultResponse = new Response([
                        'response'      => 200,
                        'description'   => 'ok',
                        '_context'      => $methodContext,
                    ]);

                    foreach ($requestMethods as $requestMethod)
                    {
                        $operationClassName = '\OpenApi\Annotations\\' . ucfirst(strtolower($requestMethod));

                        /** @var AnnotationsOperation $operationAnnotation */
                        $operationAnnotation = new $operationClassName([
                            'path'          => $requestPath,
                            'parameters'    => $requestParameters,
                            'responses'     => [
                                $defaultResponse,
                            ],
                            'tags'          => [$controllerClass],
                            '_context'      => $methodContext,
                        ]);
                        if ($requestBody)
                        {
                            $operationAnnotation->requestBody = $requestBody;
                        }

                        $analysis->addAnnotation($operationAnnotation, $methodContext);
                    }
                }
            }
        }
    }

    private function getDocParam(array $docParams, string $paramName): ?\phpDocumentor\Reflection\DocBlock\Tags\Param
    {
        foreach ($docParams as $param)
        {
            /** @var \phpDocumentor\Reflection\DocBlock\Tags\Param $param */
            if ($paramName === $param->getVariableName())
            {
                return $param;
            }
        }

        return null;
    }
}
