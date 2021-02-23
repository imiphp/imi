<?php

declare(strict_types=1);

namespace Imi\Aop\Listener;

use Imi\Aop\AopAnnotationLoader;
use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Util\File;

class BuildRuntimeListener implements IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param EventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e)
    {
        if (!Config::get('@app.imi.runtime.aop', true))
        {
            return;
        }
        ['fileName' => $fileName] = $e->getData();
        $fileName = File::path($fileName, 'aop.cache');
        AopAnnotationLoader::saveMap($fileName);
    }
}
