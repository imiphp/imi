<?php

declare(strict_types=1);

namespace Imi\Server\WebSocket\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Server\WebSocket\Route\Annotation\WSController;
use Imi\Util\Traits\TServerAnnotationParser;

/**
 * 控制器注解处理器.
 */
class WSControllerParser extends BaseParser
{
    use TServerAnnotationParser;

    public function __construct()
    {
        $this->controllerAnnotationClass = WSController::class;
    }

    /**
     * {@inheritDoc}
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName): void
    {
    }
}
