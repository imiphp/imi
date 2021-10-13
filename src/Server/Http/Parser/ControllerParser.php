<?php

declare(strict_types=1);

namespace Imi\Server\Http\Parser;

use Imi\Bean\Parser\BaseParser;
use Imi\Server\Http\Route\Annotation\Controller;
use Imi\Util\Traits\TServerAnnotationParser;

/**
 * 控制器注解处理器.
 */
class ControllerParser extends BaseParser
{
    use TServerAnnotationParser;

    public function __construct()
    {
        $this->controllerAnnotationClass = Controller::class;
    }

    /**
     * {@inheritDoc}
     */
    public function parse(\Imi\Bean\Annotation\Base $annotation, string $className, string $target, string $targetName): void
    {
    }
}
