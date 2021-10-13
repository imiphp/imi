<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Server\TcpServer\Route\Annotation\TcpController;
use Imi\Util\Traits\TServerAnnotationParser;

/**
 * 控制器注解处理器.
 */
class TcpControllerParser extends BaseParser
{
    use TServerAnnotationParser;

    public function __construct()
    {
        $this->controllerAnnotationClass = TcpController::class;
    }

    /**
     * {@inheritDoc}
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName): void
    {
    }
}
