<?php

declare(strict_types=1);

namespace Imi\Core\Runtime\Contract;

/**
 * 运行时模式处理器.
 */
interface IRuntimeModeHandler
{
    /**
     * 初始化.
     *
     * @return void
     */
    public function init();
}
