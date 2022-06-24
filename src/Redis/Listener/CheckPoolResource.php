<?php

declare(strict_types=1);

namespace Imi\Redis\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Log\Log;
use Imi\Pool\PoolManager;
use Imi\Redis\RedisManager;

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
        if ($connections = Config::get('@app.redis.connections'))
        {
            foreach ($connections as $name => $_)
            {
                if (!PoolManager::exists($name))
                {
                    try
                    {
                        $redis = RedisManager::getInstance($name);
                        if ($redis->isConnected())
                        {
                            $redis->close();
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
                        Log::error(sprintf('The Redis [%s] are not available', $name));
                        $result = false;
                    }
                }
            }
        }
    }
}
