<?php

declare(strict_types=1);

namespace Imi\Aop;

use Imi\Aop\Model\AopItem;
use Imi\Util\Imi;

/**
 * Aop 管理器.
 */
class AopManager
{
    /**
     * @var AopItem[][][]
     */
    private static array $cache = [];

    private static array $arrayCache = [];

    /**
     * @var AopItem[][][][]
     */
    private static array $parsedCache = [];

    private function __construct()
    {
    }

    public static function getCache(): array
    {
        return self::$cache;
    }

    public static function setCache(array $cache): void
    {
        self::$cache = $cache;
    }

    public static function getArrayCache(): array
    {
        if (!self::$arrayCache)
        {
            $arrayCache = self::$cache;
            foreach ($arrayCache as $k1 => $v1)
            {
                foreach ($v1 as $k2 => $v2)
                {
                    $arrayCache[$k1][$k2] = serialize($v2);
                }
            }

            return self::$arrayCache = $arrayCache;
        }

        return self::$arrayCache;
    }

    public static function setArrayCache(array $arrayCache): void
    {
        self::$arrayCache = $arrayCache;
    }

    public static function clear(): void
    {
        self::$cache = self::$parsedCache = [];
    }

    public static function addBefore(string $class, string $methodRule, callable $callback, int $priority = 0, array $options = []): AopItem
    {
        self::$cache[$class]['before'][] = $result = new AopItem($class, $methodRule, $callback, $priority, $options);
        if (isset(self::$parsedCache[$class]['before']))
        {
            unset(self::$parsedCache[$class]['before']);
        }

        return $result;
    }

    public static function addAfter(string $class, string $methodRule, callable $callback, int $priority = 0, array $options = []): AopItem
    {
        self::$cache[$class]['after'][] = $result = new AopItem($class, $methodRule, $callback, $priority, $options);
        if (isset(self::$parsedCache[$class]['after']))
        {
            unset(self::$parsedCache[$class]['after']);
        }

        return $result;
    }

    public static function addAround(string $class, string $methodRule, callable $callback, int $priority = 0, array $options = []): AopItem
    {
        self::$cache[$class]['around'][] = $result = new AopItem($class, $methodRule, $callback, $priority, $options);
        if (isset(self::$parsedCache[$class]['around']))
        {
            unset(self::$parsedCache[$class]['around']);
        }

        return $result;
    }

    public static function addAfterReturning(string $class, string $methodRule, callable $callback, int $priority = 0, array $options = []): AopItem
    {
        self::$cache[$class]['afterReturning'][] = $result = new AopItem($class, $methodRule, $callback, $priority, $options);
        if (isset(self::$parsedCache[$class]['afterReturning']))
        {
            unset(self::$parsedCache[$class]['afterReturning']);
        }

        return $result;
    }

    public static function addAfterThrowing(string $class, string $methodRule, callable $callback, int $priority = 0, array $options = []): AopItem
    {
        self::$cache[$class]['afterThrowing'][] = $result = new AopItem($class, $methodRule, $callback, $priority, $options);
        if (isset(self::$parsedCache[$class]['afterThrowing']))
        {
            unset(self::$parsedCache[$class]['afterThrowing']);
        }

        return $result;
    }

    /**
     * @return AopItem[]
     */
    public static function getBeforeItems(string $class, string $method): array
    {
        if (isset(self::$parsedCache[$class]['before'][$method]))
        {
            return self::$parsedCache[$class]['before'][$method];
        }
        $result = new \SplPriorityQueue();
        if (!isset(self::$cache[$class]['before']) && isset(self::$arrayCache[$class]['before']))
        {
            self::$cache[$class]['before'] = unserialize(self::$arrayCache[$class]['before']);
        }
        if (isset(self::$cache[$class]['before']))
        {
            /** @var AopItem $aopItem */
            foreach (self::$cache[$class]['before'] as $aopItem)
            {
                if (Imi::checkRuleMatch($aopItem->getMethodRule(), $method))
                {
                    $options = $aopItem->getOptions();
                    if (isset($options['deny']))
                    {
                        $deny = false;
                        foreach ($options['deny'] as $rule)
                        {
                            if (Imi::checkClassMethodRule($rule, $class, $method))
                            {
                                $deny = true;
                                break;
                            }
                        }
                        if ($deny)
                        {
                            continue;
                        }
                    }
                    $result->insert($aopItem, $aopItem->getPriority());
                }
            }
        }

        return self::$parsedCache[$class]['before'][$method] = iterator_to_array($result);
    }

    /**
     * @return AopItem[]
     */
    public static function getAfterItems(string $class, string $method): array
    {
        if (isset(self::$parsedCache[$class]['after'][$method]))
        {
            return self::$parsedCache[$class]['after'][$method];
        }
        $result = new \SplPriorityQueue();
        if (!isset(self::$cache[$class]['after']) && isset(self::$arrayCache[$class]['after']))
        {
            self::$cache[$class]['after'] = unserialize(self::$arrayCache[$class]['after']);
        }
        if (isset(self::$cache[$class]['after']))
        {
            /** @var AopItem $aopItem */
            foreach (self::$cache[$class]['after'] ?? [] as $aopItem)
            {
                if (Imi::checkRuleMatch($aopItem->getMethodRule(), $method))
                {
                    $options = $aopItem->getOptions();
                    if (isset($options['deny']))
                    {
                        $deny = false;
                        foreach ($options['deny'] as $rule)
                        {
                            if (Imi::checkClassMethodRule($rule, $class, $method))
                            {
                                $deny = true;
                                break;
                            }
                        }
                        if ($deny)
                        {
                            continue;
                        }
                    }
                    $result->insert($aopItem, $aopItem->getPriority());
                }
            }
        }

        return self::$parsedCache[$class]['after'][$method] = iterator_to_array($result);
    }

    /**
     * @return AopItem[]
     */
    public static function getAroundItems(string $class, string $method): array
    {
        if (isset(self::$parsedCache[$class]['around'][$method]))
        {
            return self::$parsedCache[$class]['around'][$method];
        }
        $result = new \SplPriorityQueue();
        if (!isset(self::$cache[$class]['around']) && isset(self::$arrayCache[$class]['around']))
        {
            self::$cache[$class]['around'] = unserialize(self::$arrayCache[$class]['around']);
        }
        if (isset(self::$cache[$class]['around']))
        {
            /** @var AopItem $aopItem */
            foreach (self::$cache[$class]['around'] ?? [] as $aopItem)
            {
                if (Imi::checkRuleMatch($aopItem->getMethodRule(), $method))
                {
                    $options = $aopItem->getOptions();
                    if (isset($options['deny']))
                    {
                        $deny = false;
                        foreach ($options['deny'] as $rule)
                        {
                            if (Imi::checkClassMethodRule($rule, $class, $method))
                            {
                                $deny = true;
                                break;
                            }
                        }
                        if ($deny)
                        {
                            continue;
                        }
                    }
                    $result->insert($aopItem, $aopItem->getPriority());
                }
            }
        }

        return self::$parsedCache[$class]['around'][$method] = iterator_to_array($result);
    }

    /**
     * @return AopItem[]
     */
    public static function getAfterReturningItems(string $class, string $method): array
    {
        if (isset(self::$parsedCache[$class]['afterReturning'][$method]))
        {
            return self::$parsedCache[$class]['afterReturning'][$method];
        }
        $result = new \SplPriorityQueue();
        if (!isset(self::$cache[$class]['afterReturning']) && isset(self::$arrayCache[$class]['afterReturning']))
        {
            self::$cache[$class]['afterReturning'] = unserialize(self::$arrayCache[$class]['afterReturning']);
        }
        if (isset(self::$cache[$class]['afterReturning']))
        {
            /** @var AopItem $aopItem */
            foreach (self::$cache[$class]['afterReturning'] ?? [] as $aopItem)
            {
                if (Imi::checkRuleMatch($aopItem->getMethodRule(), $method))
                {
                    $options = $aopItem->getOptions();
                    if (isset($options['deny']))
                    {
                        $deny = false;
                        foreach ($options['deny'] as $rule)
                        {
                            if (Imi::checkClassMethodRule($rule, $class, $method))
                            {
                                $deny = true;
                                break;
                            }
                        }
                        if ($deny)
                        {
                            continue;
                        }
                    }
                    $result->insert($aopItem, $aopItem->getPriority());
                }
            }
        }

        return self::$parsedCache[$class]['afterReturning'][$method] = iterator_to_array($result);
    }

    /**
     * @return AopItem[]
     */
    public static function getAfterThrowingItems(string $class, string $method): array
    {
        if (isset(self::$parsedCache[$class]['afterThrowing'][$method]))
        {
            return self::$parsedCache[$class]['afterThrowing'][$method];
        }
        $result = new \SplPriorityQueue();
        if (!isset(self::$cache[$class]['afterThrowing']) && isset(self::$arrayCache[$class]['afterThrowing']))
        {
            self::$cache[$class]['afterThrowing'] = unserialize(self::$arrayCache[$class]['afterThrowing']);
        }
        if (isset(self::$cache[$class]['afterThrowing']))
        {
            /** @var AopItem $aopItem */
            foreach (self::$cache[$class]['afterThrowing'] ?? [] as $aopItem)
            {
                if (Imi::checkRuleMatch($aopItem->getMethodRule(), $method))
                {
                    $options = $aopItem->getOptions();
                    if (isset($options['deny']))
                    {
                        $deny = false;
                        foreach ($options['deny'] as $rule)
                        {
                            if (Imi::checkClassMethodRule($rule, $class, $method))
                            {
                                $deny = true;
                                break;
                            }
                        }
                        if ($deny)
                        {
                            continue;
                        }
                    }
                    $result->insert($aopItem, $aopItem->getPriority());
                }
            }
        }

        return self::$parsedCache[$class]['afterThrowing'][$method] = iterator_to_array($result);
    }
}
