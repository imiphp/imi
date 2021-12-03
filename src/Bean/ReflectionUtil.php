<?php

declare(strict_types=1);

namespace Imi\Bean;

use InvalidArgumentException;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

class ReflectionUtil
{
    private function __construct()
    {
    }

    public static function getTypeComments(?ReflectionType $type, ?string $className = null): string
    {
        if (!$type)
        {
            return 'mixed';
        }
        if ($type instanceof ReflectionNamedType)
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
                else
                {
                    $typeStr = '\\' . $typeStr;
                }
            }
            if ($type->allowsNull() && 'mixed' !== $typeStr)
            {
                return $typeStr . '|null';
            }
            else
            {
                return $typeStr;
            }
        }
        elseif ($type instanceof ReflectionUnionType)
        {
            $result = [];
            foreach ($type->getTypes() as $subType)
            {
                $result[] = self::getTypeCode($subType, $className);
            }
            if ($type->allowsNull() && !\in_array('mixed', $result))
            {
                $result[] = 'null';
            }

            return implode('|', $result);
        }
        else
        {
            throw new InvalidArgumentException(sprintf('Unknown type %s', \get_class($type)));
        }
    }

    public static function getTypeCode(?ReflectionType $type, ?string $className = null): string
    {
        if (!$type)
        {
            return '';
        }
        if ($type instanceof ReflectionNamedType)
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
                else
                {
                    $typeStr = '\\' . $typeStr;
                }
            }
            if ($type->allowsNull() && 'mixed' !== $typeStr)
            {
                return '?' . $typeStr;
            }
            else
            {
                return $typeStr;
            }
        }
        elseif ($type instanceof ReflectionUnionType)
        {
            $result = [];
            foreach ($type->getTypes() as $subType)
            {
                $result[] = self::getTypeCode($subType, $className);
            }
            if ($type->allowsNull() && !\in_array('mixed', $result))
            {
                $result[] = 'null';
            }

            return implode('|', $result);
        }
        else
        {
            throw new InvalidArgumentException(sprintf('Unknown type %s', \get_class($type)));
        }
    }

    public static function allowsType(ReflectionType $type, string $checkType, ?string $className = null): bool
    {
        if ('null' === $checkType)
        {
            return $type->allowsNull();
        }
        if ($type instanceof ReflectionNamedType)
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
                else
                {
                    $typeStr = $typeStr;
                }
            }

            return $typeStr === $checkType || is_subclass_of($checkType, $typeStr);
        }
        if ($type instanceof ReflectionUnionType)
        {
            foreach ($type->getTypes() as $subType)
            {
                $typeStr = ltrim(self::getTypeCode($subType, $className), '\\');
                if ($typeStr === $checkType || is_subclass_of($checkType, $typeStr))
                {
                    return true;
                }
            }

            return false;
        }
        throw new InvalidArgumentException(sprintf('Unknown type %s', \get_class($type)));
    }
}
