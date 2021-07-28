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
            if ($type->allowsNull())
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
            if ($type->allowsNull())
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
            if ($type->allowsNull())
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
            if ($type->allowsNull())
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
}
