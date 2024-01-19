<?php

declare(strict_types=1);

namespace Imi\Model\IdGenerator;

/**
 * UUID 生成器类型.
 */
enum UUIDGeneratorType
{
    case Time;

    case Random;

    case MD5;

    case SHA1;
}
