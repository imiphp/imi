<?php

declare(strict_types=1);

namespace Imi\Model\Traits;

use Imi\Model\Annotation\Column;
use Imi\Model\Meta;

trait TListValue
{
    protected static function parseListInitValue(string $name, mixed $value, Column $fieldAnnotation, Meta $meta): mixed
    {
        if ('' === $value)
        {
            return [];
        }
        elseif (null !== $fieldAnnotation->listSeparator)
        {
            return '' === $fieldAnnotation->listSeparator ? [] : explode($fieldAnnotation->listSeparator, (string) $value);
        }

        return $value;
    }

    protected static function parseListSaveValue(string $name, mixed $value, Column $fieldAnnotation, Meta $meta): mixed
    {
        if (null !== $value && null !== $fieldAnnotation->listSeparator)
        {
            $value = implode($fieldAnnotation->listSeparator, $value);
        }

        return $value;
    }
}
