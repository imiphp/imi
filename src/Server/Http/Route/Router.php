<?php

declare(strict_types=1);

namespace Imi\Server\Http\Route;

use Psr\Http\Message\ServerRequestInterface;

class Router
{
    public const NOT_FOUND = 0;
    public const FOUND = 1;
    public const METHOD_NOT_ALLOWED = 2;

    public const ROUTE_PATH = 0;
    public const ROUTE_CALLABLE = 1;
    public const ROUTE_METHOD = 2;
    public const ROUTE_CASE_INSENSITIVE = 3;
    public const ROUTE_CHECK_CALLABLES = 4;
    public const ROUTE_STATIC = 5;
    public const ROUTE_DATA = 6;

    protected array $staticRoutes = [];

    protected array $dynamicRoutes = [];

    /**
     * @param string|string[] $method
     */
    public function add(string $path, callable $callable, $method = null, bool $caseInsensitive = false, array $checkCallables = [], array $data = []): void
    {
        $this->dynamicRoutes[] = [
            $path,
            $callable,
            null === $method ? null : (array) $method,
            $caseInsensitive,
            $checkCallables,
            false,
            $data,
        ];
    }

    /**
     * @param string|string[] $method
     */
    public function addStatic(string $path, callable $callable, $method = null, bool $caseInsensitive = false, array $checkCallables = [], array $data = []): void
    {
        $this->staticRoutes[$path][] = [
            $path,
            $callable,
            null === $method ? null : (array) $method,
            $caseInsensitive,
            $checkCallables,
            true,
            $data,
        ];
    }

    public function dispatch(ServerRequestInterface $request): array
    {
        $path = $request->getUri()->getPath();
        $routes = $this->staticRoutes;
        if (isset($routes[$path]))
        {
            $result = $this->dispatchRoutes($request, $routes[$path], false);
            if ($result && self::FOUND === $result[0])
            {
                return $result;
            }
        }
        $tmpResult = $this->dispatchRoutes($request, $this->dynamicRoutes, true);
        if ($tmpResult)
        {
            if (self::FOUND === $tmpResult[0])
            {
                return $tmpResult;
            }
            $result = $tmpResult;
        }

        return $result ?? [
            self::NOT_FOUND,
        ];
    }

    public function dispatchRoutes(ServerRequestInterface $request, array $routes, bool $parsePath): ?array
    {
        if ($parsePath)
        {
            $requestPath = $request->getUri()->getPath();
        }
        foreach ($routes as $route)
        {
            $data = [];
            if ($parsePath)
            {
                if ($route[self::ROUTE_STATIC])
                {
                    // 静态匹配地址
                    if ($route[self::ROUTE_CASE_INSENSITIVE])
                    {
                        // @phpstan-ignore-next-line
                        if (0 !== strcasecmp($requestPath, $route[self::ROUTE_PATH]))
                        {
                            continue;
                        }
                    }
                    else
                    {
                        // @phpstan-ignore-next-line
                        if ($requestPath !== $route[self::ROUTE_PATH])
                        {
                            continue;
                        }
                    }
                }
                else
                {
                    // 正则匹配地址
                    $pattern = $this->getPathPattern($route[self::ROUTE_PATH], $route[self::ROUTE_CASE_INSENSITIVE], $fields);
                    // @phpstan-ignore-next-line
                    if (preg_match_all($pattern, $requestPath, $matches))
                    {
                        foreach ($fields as $i => $fieldName)
                        {
                            $data[$fieldName] = $matches[$i + 1][0];
                        }
                    }
                    else
                    {
                        continue;
                    }
                }
            }

            // 请求方法
            $method = $route[self::ROUTE_METHOD];
            if (null !== $method && !\in_array($request->getMethod(), $method))
            {
                $result = [
                    self::METHOD_NOT_ALLOWED,
                    $route,
                ];
                continue;
            }

            // 自定义回调
            if ($checkCallables = $route[self::ROUTE_CHECK_CALLABLES])
            {
                foreach ($checkCallables as $checkCallable)
                {
                    if (false === $checkCallable($request, $route, $data, $this))
                    {
                        continue 2;
                    }
                }
            }

            return [
                self::FOUND,
                $route,
                $data,
            ];
        }

        return $result ?? null;
    }

    public function getPathPattern(string $path, bool $caseInsensitive, ?array &$fields): string
    {
        $pathPatternCache = &$this->pathPatternCache;
        if (isset($pathPatternCache[$path][$caseInsensitive]))
        {
            $cache = $pathPatternCache[$path][$caseInsensitive];
            $fields = $cache['fields'];

            return $cache['pattern'];
        }
        else
        {
            $fields = [];
            if (str_contains($path, '/'))
            {
                $parsedPattern = str_replace('/', '\/', $path);
            }
            else
            {
                $parsedPattern = $path;
            }
            $pattern = '/^' . preg_replace_callback(
                '/\{(([^\}:]+?)|([^:]+?):(?:([^{}]*(?:\{(?-1)\}[^{}]*)*))?)\}/',
                static function (array $matches) use (&$fields): string {
                    if (isset($matches[4]))
                    {
                        // 正则
                        $fields[] = $matches[3];

                        return '(' . $matches[4] . ')';
                    }
                    else
                    {
                        // 正常匹配
                        $fields[] = $matches[1];

                        return '(.+)';
                    }
                },
                $parsedPattern, -1
            ) . '\/?$/' . ($caseInsensitive ? 'i' : '');
            $pathPatternCache[$path][$caseInsensitive] = [
                'pattern' => $pattern,
                'fields'  => $fields,
            ];

            return $pattern;
        }
    }

    public function getStaticRoutes(): array
    {
        return $this->staticRoutes;
    }

    public function getDynamicRoutes(): array
    {
        return $this->dynamicRoutes;
    }

    public function setStaticRoutes(array $staticRoutes): self
    {
        $this->staticRoutes = $staticRoutes;

        return $this;
    }

    public function setDynamicRoutes(array $dynamicRoutes): self
    {
        $this->dynamicRoutes = $dynamicRoutes;

        return $this;
    }
}
