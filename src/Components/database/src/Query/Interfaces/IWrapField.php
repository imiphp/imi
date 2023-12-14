<?php

declare(strict_types=1);

namespace Imi\Db\Query\Interfaces;

interface IWrapField extends IField
{
    public function getSubFields(): array;

    public function setSubFields(array $subFields): void;

    public function getWrapLeft(): string;

    public function setWrapLeft(string $wrapLeft): void;

    public function getWrapRight(): string;

    public function setWrapRight(string $wrapRight): void;
}
