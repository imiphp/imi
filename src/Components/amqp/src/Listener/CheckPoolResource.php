<?php

declare(strict_types=1);

namespace Imi\AMQP\Listener;

use Imi\AMQP\Pool\AMQPPool;
use Imi\Bean\Annotation\Listener;
use Imi\Config;
use Imi\Event\IEventListener;
use Imi\Log\Log;
use Imi\Pool\Event\CheckPoolResourceEvent;
use Imi\Pool\PoolManager;

#[Listener(eventName: 'IMI.CHECK_POOL_RESOURCE')]
class CheckPoolResource implements IEventListener
{
    /**
     * @param CheckPoolResourceEvent $e
     */
    public function handle(\Imi\Event\Contract\IEvent $e): void
    {
        if ($connections = Config::get('@app.amqp.connections'))
        {
            $result = &$e->result;
            foreach ($connections as $name => $_)
            {
                if (!PoolManager::exists($name))
                {
                    try
                    {
                        $instance = AMQPPool::getInstance($name);
                        if ($instance->isConnected())
                        {
                            $instance->close();
                        }
                        else
                        {
                            $result = false;
                        }
                    }
                    catch (\Throwable $th)
                    {
                        Log::error($th);
                        Log::error(sprintf('The AMQP [%s] are not available', $name));
                        $result = false;
                    }
                }
            }
        }
    }
}
