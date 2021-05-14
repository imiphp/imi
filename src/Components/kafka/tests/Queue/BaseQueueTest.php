<?php

namespace Imi\Kafka\Test\Queue;

use Imi\Kafka\Queue\KafkaQueueDriver;
use Imi\Queue\Driver\IQueueDriver;
use Imi\Queue\Model\Message;
use PHPUnit\Framework\TestCase;

abstract class BaseQueueTest extends TestCase
{
    /**
     * @return KafkaQueueDriver
     */
    abstract protected function getDriver(): IQueueDriver;

    public function testPush(): void
    {
        $driver = $this->getDriver();

        $message = new Message();
        $message->setMessage('testPush');
        $messageId = $driver->push($message);
        $this->assertEquals('', $messageId);
    }

    public function testPop(): void
    {
        $driver = $this->getDriver();
        try
        {
            $i = 10;
            while ($i--)
            {
                $message = $driver->pop();
                if ($message)
                {
                    break;
                }
            }
            $this->assertInstanceOf(\Imi\Queue\Contract\IMessage::class, $message);
            $this->assertEquals('', $messageId);
            $this->assertEquals('testPush', $message->getMessage());
            $driver->success($message);
        }
        finally
        {
            $driver->close();
        }
    }
}
