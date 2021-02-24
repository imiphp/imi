<?php

declare(strict_types=1);

namespace Imi\Core\Runtime;

use Imi\App;
use Imi\Core\Runtime\Contract\IRuntimeModeHandler;

class Runtime
{
    private static IRuntimeModeHandler $runtimeModeHandler;

    private function __construct()
    {
    }

    public static function setRuntimeModeHandler(string $class): IRuntimeModeHandler
    {
        return self::$runtimeModeHandler = App::getBean($class);
    }

    public static function getRuntimeModeHandler(): IRuntimeModeHandler
    {
        return self::$runtimeModeHandler;
    }
}
