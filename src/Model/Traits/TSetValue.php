<?php

declare(strict_types=1);

namespace Imi\Model\Traits;

use Imi\Model\Annotation\Column;
use Imi\Model\Meta;

trait TSetValue
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected static function parseSetInitValue(string $name, $value, Column $fieldAnnotation, Meta $meta)
    {
        if ('' === $value)
        {
            return [];
        }
        else
        {
            return explode(',', $value);
        }
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected static function parseSetSaveValue(string $name, $value, Column $fieldAnnotation, Meta $meta)
    {
        return implode(',', $value);
    }
}
