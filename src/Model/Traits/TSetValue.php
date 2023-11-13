<?php

declare(strict_types=1);

namespace Imi\Model\Traits;

use Imi\Model\Annotation\Column;
use Imi\Model\Meta;

trait TSetValue
{
    protected static function parseSetInitValue(string $name, mixed $value, Column $fieldAnnotation, Meta $meta): mixed
    {
        if ('' === $value)
        {
            return [];
        }
        else
        {
            return explode(',', (string) $value);
        }
    }

    protected static function parseSetSaveValue(string $name, mixed $value, Column $fieldAnnotation, Meta $meta): mixed
    {
        return implode(',', $value);
    }
}
