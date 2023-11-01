<?php

declare(strict_types=1);

namespace Imi\Db\Listener;

use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\Db\Db;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Log\Log;
use Imi\Pool\PoolManager;

#[Listener(eventName: 'IMI.CHECK_POOL_RESOURCE')]
class CheckPoolResource implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        if ($connections = Config::get('@app.db.connections'))
        {
            $result = &$e->getData()['result'];
            foreach ($connections as $name => $_)
            {
                if (!PoolManager::exists($name))
                {
                    try
                    {
                        $db = Db::getNewInstance($name);
                        if ($db->isConnected() && $db->ping())
                        {
                            $db->close();
                        }
                        else
                        {
                            $result = false;
                        }
                    }
                    catch (\Throwable $th)
                    {
                        Log::error($th);
                        Log::error(sprintf('The Db [%s] are not available', $name));
                        $result = false;
                    }
                }
            }
        }
    }
}
