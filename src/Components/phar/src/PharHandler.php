<?php

declare(strict_types=1);

namespace Imi\Phar;

use Imi\Bean\ReflectionUtil;

(static function (): void {
    $refMethod = new \ReflectionMethod(\Composer\XdebugHandler\XdebugHandler::class, 'check');
    $returnType = $refMethod->getReturnType();
    if ('void' === ReflectionUtil::getTypeCode($returnType))
    {
        require __DIR__ . '/Ignore/PharHandler3.php';
    }
    else
    {
        require __DIR__ . '/Ignore/PharHandler2.php';
    }
})();
