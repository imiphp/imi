<?php

declare(strict_types=1);

namespace Imi\Bean;

class ReflectionUtil
{
    use \Imi\Util\Traits\TStaticClass;

    public static function getTypeComments(?\ReflectionType $type, ?string $className = null): string
    {
        if (!$type)
        {
            return 'mixed';
        }
        if ($type instanceof \ReflectionNamedType)
        {
            $typeStr = $type->getName();
            if (!$type->isBuiltin())
            {
                if ('self' === $typeStr)
                {
                    if (null !== $className)
                    {
                        $typeStr = '\\' . $className;
                    }
                }
                elseif ('static' !== $typeStr)
                {
                    $typeStr = '\\' . $typeStr;
                }
            }
            if ($type->allowsNull() && !\in_array($typeStr, ['mixed', 'null']))
            {
                return $typeStr . '|null';
            }

            return $typeStr;
        }
        elseif ($type instanceof \ReflectionUnionType)
        {
            $result = [];
            $hasNull = false;
            foreach ($type->getTypes() as $subType)
            {
                $content = self::getTypeComments($subType, $className);
                if (!$hasNull && 'null' === $content)
                {
                    $hasNull = true;
                }
                if ($subType instanceof \ReflectionIntersectionType)
                {
                    $content = '(' . $content . ')';
                }
                $result[] = $content;
            }
            if (!$hasNull && $type->allowsNull() && !\in_array('mixed', $result))
            {
                $result[] = 'null';
            }

            return implode('|', $result);
        }
        elseif ($type instanceof \ReflectionIntersectionType)
        {
            $result = [];
            foreach ($type->getTypes() as $subType)
            {
                $result[] = self::getTypeComments($subType, $className);
            }

            return implode('&', $result);
        }
        else
        {
            throw new \InvalidArgumentException(sprintf('Unknown type %s', \get_class($type)));
        }
    }

    public static function getTypeCode(?\ReflectionType $type, ?string $className = null): string
    {
        if (!$type)
        {
            return '';
        }
        if ($type instanceof \ReflectionNamedType)
        {
            $typeStr = $type->getName();
            if (!$type->isBuiltin())
            {
                if ('self' === $typeStr)
                {
                    if (null !== $className)
                    {
                        $typeStr = '\\' . $className;
                    }
                }
                elseif ('static' !== $typeStr)
                {
                    $typeStr = '\\' . $typeStr;
                }
            }
            if ($type->allowsNull() && !\in_array($typeStr, ['mixed', 'null']))
            {
                return '?' . $typeStr;
            }

            return $typeStr;
        }
        elseif ($type instanceof \ReflectionUnionType)
        {
            $result = [];
            $hasNull = false;
            foreach ($type->getTypes() as $subType)
            {
                $content = self::getTypeCode($subType, $className);
                if (!$hasNull && 'null' === $content)
                {
                    $hasNull = true;
                }
                if ($subType instanceof \ReflectionIntersectionType)
                {
                    $content = '(' . $content . ')';
                }
                $result[] = $content;
            }
            if (!$hasNull && $type->allowsNull() && !\in_array('mixed', $result))
            {
                $result[] = 'null';
            }

            return implode('|', $result);
        }
        elseif ($type instanceof \ReflectionIntersectionType)
        {
            $result = [];
            foreach ($type->getTypes() as $subType)
            {
                $result[] = self::getTypeCode($subType, $className);
            }

            return implode('&', $result);
        }
        else
        {
            throw new \InvalidArgumentException(sprintf('Unknown type %s', \get_class($type)));
        }
    }

    public static function allowsType(\ReflectionType $type, string $checkType, ?string $className = null): bool
    {
        if ('' === $checkType)
        {
            return false;
        }
        if ('null' === $checkType || '?' === $checkType[0])
        {
            return $type->allowsNull();
        }
        $checkTypes = explode('|', $checkType);
        if ('?' === $checkTypes[0][0])
        {
            $checkTypes[0][0] = substr($checkTypes[0][0], 1);
        }
        if ($type instanceof \ReflectionNamedType)
        {
            $typeStr = $type->getName();
            if (!$type->isBuiltin())
            {
                if ('self' === $typeStr)
                {
                    if (null !== $className)
                    {
                        $typeStr = $className;
                    }
                }
            }

            return $typeStr === $checkType || \in_array($typeStr, $checkTypes) || is_subclass_of($checkType, $typeStr);
        }
        if ($type instanceof \ReflectionUnionType)
        {
            foreach ($type->getTypes() as $subType)
            {
                if ($subType instanceof \ReflectionIntersectionType && self::allowsType($subType, $checkType, $className))
                {
                    return true;
                }
                $typeStr = ltrim(self::getTypeCode($subType, $className), '\\');
                if ($typeStr === $checkType || \in_array($typeStr, $checkTypes) || is_subclass_of($checkType, $typeStr))
                {
                    return true;
                }
            }

            return false;
        }
        elseif ($type instanceof \ReflectionIntersectionType)
        {
            foreach ($type->getTypes() as $subType)
            {
                if (!self::allowsType($subType, $checkType, $className))
                {
                    return false;
                }
            }

            return true;
        }
        throw new \InvalidArgumentException(sprintf('Unknown type %s', \get_class($type)));
    }

    public static function isAllowReturnedType(\ReflectionType $type): bool
    {
        if ($type instanceof \ReflectionNamedType)
        {
            $name = $type->getName();
            if ('void' === $name || (\PHP_VERSION_ID >= 80000 && 'never' === $name))
            {
                return false;
            }
        }

        return true;
    }
}
