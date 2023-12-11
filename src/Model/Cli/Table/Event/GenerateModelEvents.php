<?php

declare(strict_types=1);

namespace Imi\Model\Cli\Table\Event;

use Imi\Util\Traits\TStaticClass;

final class GenerateModelEvents
{
    use TStaticClass;

    /**
     * 生成模型前置事件.
     */
    public const BEFORE_GENERATE_MODEL = 'imi.generate_model.before';

    /**
     * 生成模型后置事件.
     */
    public const AFTER_GENERATE_MODEL = 'imi.generate_model.after';
}
