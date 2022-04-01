<?php

declare(strict_types=1);

function array_column_ex(array $arr, array $column, ?string $key = null): array
{
    $result = array_map(function ($val) use ($column) {
        $item = [];
        foreach ($column as $index => $key)
        {
            if (\is_int($index))
            {
                $item[$key] = $val[$key];
            }
            else
            {
                $item[$key] = $val[$index];
            }
        }

        return $item;
    }, $arr);

    if (!empty($key))
    {
        $result = array_combine(array_column($arr, $key), $result);
    }

    return $result;
}
