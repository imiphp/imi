<?php

declare(strict_types=1);

namespace Imi\AMQP\Listener;

use Imi\AMQP\Pool\AMQPPool;
use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Config;
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
        if ($connections = Config::get('@app.amqp.connections'))
        {
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
                        /** @var \Imi\Log\ErrorLog $errorLog */
                        $errorLog = App::getBean('ErrorLog');
                        $errorLog->onException($th);
                        Log::error(sprintf('The AMQP [%s] are not available', $name));
                        $result = false;
                    }
                }
            }
        }
    }
}
