<?php

namespace Imi\Kafka\Test\Queue;

use Imi\Queue\Driver\IQueueDriver;
use Imi\Queue\Model\Message;
use PHPUnit\Framework\TestCase;

abstract class BaseQueueTest extends TestCase
{
    abstract protected function getDriver(): IQueueDriver;

    public function testPush(): void
    {
        $driver = $this->getDriver();

        $message = new Message();
        $message->setMessage('testPush');
        $messageId = $driver->push($message);
        $this->assertNotEmpty($messageId);
    }

    public function testPop(): void
    {
        $driver = $this->getDriver();
        $message = $driver->pop();
        $this->assertInstanceOf(\Imi\Queue\Contract\IMessage::class, $message);
        $this->assertNotEmpty($message->getMessageId());
        $this->assertEquals('testPush', $message->getMessage());
        $driver->success($message);
    }
}
