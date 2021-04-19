<?php

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

    public static function getTypeComments(ReflectionType $type): string
    {
        if ($type instanceof ReflectionNamedType)
        {
            if ($type->allowsNull())
            {
                return $type->getName() . '|null';
            }
            else
            {
                return $type->getName();
            }
        }
        elseif ($type instanceof ReflectionUnionType)
        {
            $result = [];
            foreach ($type->getTypes() as $subType)
            {
                $result[] = self::getTypeCode($subType);
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

    public static function getTypeCode(ReflectionType $type): string
    {
        if ($type instanceof ReflectionNamedType)
        {
            if ($type->allowsNull())
            {
                return '?' . $type->getName();
            }
            else
            {
                return $type->getName();
            }
        }
        elseif ($type instanceof ReflectionUnionType)
        {
            $result = [];
            foreach ($type->getTypes() as $subType)
            {
                $result[] = self::getTypeCode($subType);
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
