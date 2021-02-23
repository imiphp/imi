<?php

declare(strict_types=1);

namespace Imi\Cli\Listener;

use Imi\Cli\CliManager;
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
        if (!Config::get('@app.imi.runtime.cli', true))
        {
            return;
        }
        ['fileName' => $fileName] = $e->getData();
        $fileName = File::path($fileName, 'cli.cache');
        $data = [];
        $data['cli'] = CliManager::getMap();
        file_put_contents($fileName, serialize($data));
    }
}
