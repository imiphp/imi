<?php

declare(strict_types=1);

namespace Imi\Util\Format;

class PhpSerialize implements IFormat
{
    /**
     * {@inheritDoc}
     */
    public function encode(mixed $data): string
    {
        return serialize($data);
    }

    /**
     * {@inheritDoc}
     */
    public function decode(string $data): mixed
    {
        return unserialize($data);
    }
}
