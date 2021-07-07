<?php

declare(strict_types=1);

namespace Imi\Workerman\Server;

use Workerman\Worker;

class WorkermanServerWorker extends Worker
{
    /**
     * Construct.
     */
    public function __construct(string $socket_name = '', array $context_option = [])
    {
        parent::__construct($socket_name, $context_option);

        if (OS_TYPE_LINUX === static::$_OS  // if linux
            && version_compare(\PHP_VERSION, '7.0.0', 'ge') // if php >= 7.0.0
            && version_compare(php_uname('r'), '3.9', 'ge') // if kernel >=3.9
            && 'darwin' !== strtolower(php_uname('s')) // if not Mac OS
            && 0 !== strpos($socket_name, 'unix'))
        { // if not unix socket
            $this->reusePort = true;
        }
    }

    public static function getMasterPid(): int
    {
        return static::$_masterPid;
    }
}
