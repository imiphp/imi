<?php

declare(strict_types=1);

namespace Imi\Db\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\Db\Db;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Log\Log;
use Imi\Pool\PoolManager;

/**
 * @Listener(eventName="IMI.CHECK_POOL_RESOURCE")
 */
class CheckPoolResource implements IEventListener
{
    /**
     * {@inheritDoc}
     */
    public function handle(EventParam $e): void
    {
        $result = &$e->getData()['result'];
        if ($connections = Config::get('@app.db.connections'))
        {
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
                        /** @var \Imi\Log\ErrorLog $errorLog */
                        $errorLog = App::getBean('ErrorLog');
                        $errorLog->onException($th);
                        Log::error(sprintf('The Db [%s] are not available', $name));
                        $result = false;
                    }
                }
            }
        }
    }
}
