<?php

declare(strict_types=1);

namespace Imi\Bean\Exception;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends \Exception implements NotFoundExceptionInterface
{
}
