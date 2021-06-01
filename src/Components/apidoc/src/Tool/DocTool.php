<?php

declare(strict_types=1);

namespace Imi\ApiDoc\Tool;

use Imi\App;
use Imi\Bean\Annotation\AnnotationManager;
use Imi\Bean\ReflectionUtil;
use Imi\Server\Http\Route\Annotation\Action;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Server\Http\Route\Annotation\Route;
use Imi\Tool\Annotation\Arg;
use Imi\Tool\Annotation\Operation;
use Imi\Tool\Annotation\Tool;
use Imi\Tool\ArgType;
use Imi\Util\ClassObject;
use OpenApi\Analysis;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\MediaType;
use OpenApi\Annotations\Operation as AnnotationsOperation;
use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\RequestBody;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Schema;
use OpenApi\Context;
use ReflectionClass;
use ReflectionMethod;

/**
 * @Tool("doc")
 */
class DocTool
{
    /**
     * 生成 API 接口文档.
     *
     * @Operation(name="api", co=false)
     *
     * @Arg(name="to", type=ArgType::STRING, required=true, comments="生成到的目标文件名")
     * @Arg(name="namespace", type=ArgType::STRING, required=false, comments="指定扫描的命名空间，多个用半角逗号分隔")
     *
     * @return void
     */
    public function api(string $to, ?string $namespace)
    {
        // 加载服务器注解
        \Imi\Bean\Annotation::getInstance()->init(\Imi\Main\Helper::getAppMains());
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
                        $ref = new ReflectionClass($class);
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
                $ref = new ReflectionClass($class);
                $directory[] = $ref->getFileName();
            }
        }
        if (!$directory)
        {
            echo 'Api route not found!', \PHP_EOL;

            return;
        }
        // 生成
        $processors = Analysis::processors();
        array_unshift($processors, function (Analysis $analysis) use ($controllerClasses) {
            $this->parseRoute($analysis, $controllerClasses);
        });
        $openapi = \OpenApi\scan($directory, [
            'processors'    => $processors,
        ]);
        $openapi->saveAs($to);
    }

    /**
     * 处理路由.
     *
     * @return void
     */
    private function parseRoute(Analysis $analysis, array $controllerClasses)
    {
        // OpenApi 扫描
        $map = [];
        $info = null;
        foreach ($analysis->annotations as $annotation)
        {
            /** @var \OpenApi\Context $context */
            $context = $annotation->_context;
            /** @var \OpenApi\Annotations\AbstractAnnotation $annotation */
            $className = $context->namespace . '\\' . $context->class;
            $map[$className][$context->method][\get_class($annotation)][] = $annotation;
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
            $controllerAnnotation = AnnotationManager::getClassAnnotations($controllerClass, Controller::class)[0] ?? null;
            if (!$controllerAnnotation)
            {
                continue;
            }
            $refClass = new ReflectionClass($controllerClass);
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
                $refMethod = new ReflectionMethod($controllerClass, $method);
                if (!$hasOperation)
                {
                    // 自动增加个请求
                    /** @var Route $route */
                    $route = AnnotationManager::getMethodAnnotations($controllerClass, $method, Route::class)[0] ?? null;

                    // path
                    $requestPath = $route->url ?? $method;
                    if ('/' !== ($requestPath[0] ?? null))
                    {
                        $requestPath = $controllerAnnotation->prefix . $requestPath;
                    }

                    $factory = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
                    $docblock = $factory->create($refMethod->getDocComment());
                    /** @var \phpDocumentor\Reflection\DocBlock\Tags\Param[] $docParams */
                    $docParams = $docblock->getTagsByName('param');

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
                                'description'   => $docParam ? (string) $docParam->getDescription() : \OpenApi\Annotations\UNDEFINED,
                                '_context'      => $context ?? null,
                            ]);
                        }
                    }
                    else
                    {
                        $properties = [];
                        foreach ($refMethod->getParameters() as $param)
                        {
                            $docParam = $this->getDocParam($docParams, $param->getName());
                            $properties[] = new Property([
                                'property'  => $param->getName(),
                                'type'      => ReflectionUtil::getTypeCode($param->getType(), $refMethod->getDeclaringClass()->getName()),
                                'title'     => $docParam ? (string) $docParam->getDescription() : \OpenApi\Annotations\UNDEFINED,
                                '_context'  => $context ?? null,
                            ]);
                        }
                        $schema = new Schema([
                            'schema'     => $controllerClass . '::' . $method . '@request',
                            'title'      => $controllerClass . '::' . $method . '@request',
                            'type'       => 'object',
                            'properties' => $properties,
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
                        'comment'   => $refMethod->getDocComment() ?? '',
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

    /**
     * @return \phpDocumentor\Reflection\DocBlock\Tags\Param|null
     */
    private function getDocParam(array $docParams, string $paramName)
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
