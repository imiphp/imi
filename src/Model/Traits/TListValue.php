<?php

declare(strict_types=1);

namespace Imi\Model\Traits;

use Imi\Model\Annotation\Column;
use Imi\Model\Meta;

trait TListValue
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected static function parseListInitValue(string $name, $value, Column $fieldAnnotation, Meta $meta)
    {
        if ('' === $value)
        {
            return [];
        }
        elseif (null !== $fieldAnnotation->listSeparator)
        {
            return '' === $fieldAnnotation->listSeparator ? [] : explode($fieldAnnotation->listSeparator, $value);
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected static function parseListSaveValue(string $name, $value, Column $fieldAnnotation, Meta $meta)
    {
        if (null !== $value && null !== $fieldAnnotation->listSeparator)
        {
            $value = implode($fieldAnnotation->listSeparator, $value);
        }

        return $value;
    }
}
