<?php

declare(strict_types=1);

namespace Imi\Queue\Enum;

/**
 * @property string $value
 */
interface IQueueType extends \UnitEnum
{
    public function structType(): string;
}
