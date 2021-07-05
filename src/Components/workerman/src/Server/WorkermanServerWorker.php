<?php

declare(strict_types=1);

namespace Imi\Workerman\Server;

use Workerman\Worker;

class WorkermanServerWorker extends Worker
{
    /**
     * Construct.
     *
     * @param string $socket_name
     */
    public function __construct($socket_name = '', array $context_option = [])
    {
        parent::__construct($socket_name, $context_option);

        if (static::$_OS === \OS_TYPE_LINUX  // if linux
            && \version_compare(\PHP_VERSION,'7.0.0', 'ge') // if php >= 7.0.0
            && \version_compare(php_uname('r'), '3.9', 'ge') // if kernel >=3.9
            && \strtolower(\php_uname('s')) !== 'darwin' // if not Mac OS
            && strpos($socket_name,'unix') !== 0) { // if not unix socket

            $this->reusePort = true;
        }
    }

    public static function getMasterPid(): int
    {
        return static::$_masterPid;
    }
}
