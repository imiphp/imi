<?php

declare(strict_types=1);

namespace Imi\Server\UdpServer\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Server\UdpServer\Route\Annotation\UdpController;
use Imi\Util\Traits\TServerAnnotationParser;

/**
 * 控制器注解处理器.
 */
class UdpControllerParser extends BaseParser
{
    use TServerAnnotationParser;

    public function __construct()
    {
        $this->controllerAnnotationClass = UdpController::class;
    }

    /**
     * {@inheritDoc}
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName): void
    {
    }
}
