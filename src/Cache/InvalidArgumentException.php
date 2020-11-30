<?php

declare(strict_types=1);

namespace Imi\Cache;

class InvalidArgumentException extends \InvalidArgumentException implements \Psr\SimpleCache\InvalidArgumentException
{
}
