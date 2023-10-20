<?php

declare(strict_types=1);

namespace Imi;

use Imi\Validate\ValidatorHelper;

class Env
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public static function get(?string $varname = null, $default = null)
    {
        $result = $_ENV[$varname] ?? $_SERVER[$varname] ?? getenv($varname);
        if (false === $result)
        {
            return $default;
        }
        // @phpstan-ignore-next-line
        if (\is_array($result))
        {
            return $result;
        }
        $lower = strtolower((string) $result);
        if ('null' === $lower)
        {
            return null;
        }

        switch (\gettype($default))
        {
            case 'NULL':
                if (ValidatorHelper::int($result))
                {
                    return (int) $result;
                }
                if (is_numeric($result))
                {
                    return (float) $result;
                }
                if ('false' === $lower || 'off' === $lower)
                {
                    return false;
                }
                if ('true' === $lower || 'on' === $lower)
                {
                    return true;
                }

                return $result;
            case 'integer':
                return (int) $result;
            case 'double':
                return (float) $result;
            case 'boolean':
                if ('true' === $lower || 'on' === $lower || '1' === $lower)
                {
                    return true;
                }
                if ('false' === $lower || 'off' === $lower || '0' === $lower)
                {
                    return false;
                }
                break;
            case 'array':
                $value = json_decode((string) $result, true);
                if (\JSON_ERROR_NONE !== json_last_error())
                {
                    $value = explode(',', (string) $result);
                    // @phpstan-ignore-next-line
                    if (false === $value)
                    {
                        return $result;
                    }
                }

                return $value;
        }

        return $result;
    }

    public static function str(string $varname, ?string $default = null): ?string
    {
        $result = $_ENV[$varname] ?? $_SERVER[$varname] ?? getenv($varname);
        if (false === $result)
        {
            return $default;
        }
        if ('null' === strtolower((string) $result))
        {
            return null;
        }

        return $result;
    }

    public static function int(string $varname, ?int $default = 0): ?int
    {
        $result = $_ENV[$varname] ?? $_SERVER[$varname] ?? getenv($varname);
        if (false === $result)
        {
            return $default;
        }
        if ('null' === strtolower((string) $result))
        {
            return null;
        }

        if (!ValidatorHelper::int($result))
        {
            throw new \InvalidArgumentException(sprintf('Invalid int value %s', $result));
        }

        return (int) $result;
    }

    public static function float(string $varname, ?float $default = 0): ?float
    {
        $result = $_ENV[$varname] ?? $_SERVER[$varname] ?? getenv($varname);
        if (false === $result)
        {
            return $default;
        }
        if ('null' === strtolower((string) $result))
        {
            return null;
        }

        if (!ValidatorHelper::number($result))
        {
            throw new \InvalidArgumentException(sprintf('Invalid float value %s', $result));
        }

        return (float) $result;
    }

    public static function double(string $varname, ?float $default = 0): ?float
    {
        return self::float($varname, $default);
    }

    public static function bool(string $varname, ?bool $default = null): ?bool
    {
        $result = $_ENV[$varname] ?? $_SERVER[$varname] ?? getenv($varname);
        if (false === $result)
        {
            return $default;
        }
        $lower = strtolower((string) $result);
        if ('null' === $lower)
        {
            return null;
        }

        if ('true' === $lower || 'on' === $lower || '1' === $lower)
        {
            return true;
        }
        if ('false' === $lower || 'off' === $lower || '0' === $lower)
        {
            return false;
        }
        throw new \InvalidArgumentException(sprintf('Invalid bool value %s', $result));
    }

    /**
     * @param array|object|null $default
     *
     * @return array|object|null
     */
    public static function json(?string $varname = null, $default = null, ?bool $associative = true, int $depth = 512, int $flags = \JSON_THROW_ON_ERROR)
    {
        $result = $_ENV[$varname] ?? $_SERVER[$varname] ?? getenv($varname);
        if (false === $result)
        {
            return $default;
        }
        try
        {
            $value = json_decode((string) $result, $associative, $depth, $flags);
            if (!$value && \JSON_ERROR_NONE !== json_last_error())
            {
                throw new \InvalidArgumentException(sprintf('Invalid json value %s', $result));
            }
        }
        catch (\JsonException)
        {
            throw new \InvalidArgumentException(sprintf('Invalid json value %s', $result));
        }
        if (false === $value)
        {
            return $default;
        }

        return $value;
    }

    public static function list(?string $varname = null, ?array $default = null, string $separator = ',', int $limit = \PHP_INT_MAX): ?array
    {
        $result = $_ENV[$varname] ?? $_SERVER[$varname] ?? getenv($varname);
        if (false === $result)
        {
            return $default;
        }
        if ('null' === strtolower((string) $result))
        {
            return null;
        }
        // @phpstan-ignore-next-line
        if ('' === $result || false === ($value = explode($separator, (string) $result, $limit)))
        {
            throw new \InvalidArgumentException(sprintf('Invalid list value %s', $result));
        }

        return $value;
    }
}
