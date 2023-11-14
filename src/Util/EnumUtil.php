<?php

declare(strict_types=1);

namespace Imi\Util;

use Imi\Util\Traits\TStaticClass;

class EnumUtil
{
    use TStaticClass;

    public static function fromName(string $enum, string $case): \UnitEnum
    {
        $result = static::tryFromName($enum, $case);
        if ($result)
        {
            return $result;
        }
        throw new \ValueError('"' . $case . '" is not a valid name for enum "' . static::class . '"');
    }

    public static function tryFromName(string $enum, string $case): ?\UnitEnum
    {
        foreach ($enum::cases() as $c)
        {
            if ($c->name === $case)
            {
                return $c;
            }
        }

        return null;
    }

    public static function in(string $enum, mixed $value): bool
    {
        foreach ($enum::cases() as $case)
        {
            if ($case === $value || ($case->value ?? $case->name) === $value)
            {
                return true;
            }
        }

        return false;
    }
}
