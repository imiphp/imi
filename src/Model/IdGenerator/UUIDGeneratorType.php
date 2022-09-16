<?php

declare(strict_types=1);

namespace Imi\Model\IdGenerator;

use Imi\Enum\BaseEnum;

/**
 * UUID 生成器类型.
 */
class UUIDGeneratorType extends BaseEnum
{
    public const TIME = 'time';

    public const RANDOM = 'random';

    public const MD5 = 'md5';

    public const SHA1 = 'sha1';

    private function __construct()
    {
    }
}
