<?php

declare(strict_types=1);

namespace Imi\Model\IdGenerator;

/**
 * UUID 生成器类型.
 */
enum UUIDGeneratorType
{
    public const Time = 'time';

    public const Random = 'random';

    public const Md5 = 'md5';

    public const Sha1 = 'sha1';
}
