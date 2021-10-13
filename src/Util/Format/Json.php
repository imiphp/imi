<?php

declare(strict_types=1);

namespace Imi\Util\Format;

class Json implements IFormat
{
    /**
     * {@inheritDoc}
     */
    public function encode($data): string
    {
        return json_encode($data);
    }

    /**
     * {@inheritDoc}
     */
    public function decode(string $data)
    {
        return json_decode($data, true);
    }
}
