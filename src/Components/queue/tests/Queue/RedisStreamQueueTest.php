<?php

declare(strict_types=1);

namespace Imi\Queue\Test\Queue;

use Imi\App;
use Imi\Queue\Driver\IQueueDriver;
use Imi\Queue\Model\Message;

class RedisStreamQueueTest extends BaseQueueTest
{
    protected function getDriver(): IQueueDriver
    {
        // @phpstan-ignore-next-line
        return App::getBean('RedisStreamQueueDriver', 'imi-queue-stream-test', [
            'workingTimeout' => 1,
        ]);
    }

    public function testPushDelay(): void
    {
        $this->markTestSkipped();
    }

    public function testClearAndStatus(): void
    {
        $driver = $this->getDriver();
        $driver->clear();
        $status = $driver->status();
        $this->assertEquals(0, $status->getReady());
        $this->assertEquals(0, $status->getWorking());
        $this->assertEquals(0, $status->getDelay());
        $this->assertEquals(0, $status->getTimeout());
        $this->assertEquals(0, $status->getFail());

        $message = new Message();
        $message->setMessage('a');
        $messageId = $driver->push($message);
        $this->assertNotEmpty($messageId);

        $status = $driver->status();
        $this->assertEquals(0, $status->getFail());
        $this->assertEquals(0, $status->getWorking());

        $mesasge = $driver->pop();
        $this->assertNotEmpty($mesasge);

        $status = $driver->status();
        $this->assertEquals(0, $status->getFail());
        $this->assertEquals(1, $status->getWorking());

        $driver->fail($mesasge);

        $status = $driver->status();
        $this->assertEquals(1, $status->getFail());
        $this->assertEquals(0, $status->getWorking());
    }

    public function testRestoreFailMessages(): void
    {
        $driver = $this->getDriver();
        $driver->clear();

        $message = new Message();
        $message->setMessage('a');
        $messageId = $driver->push($message);
        $this->assertNotEmpty($messageId);

        $message = $this->getDriver()->pop();
        $this->assertNotEmpty($message->getMessageId());

        $driver->fail($message);

        $this->assertEquals(1, $driver->restoreFailMessages());

        // requeue
        $message = $this->getDriver()->pop();
        $this->assertNotEmpty($message->getMessageId());

        $driver->fail($message, true);

        $this->assertEquals(0, $driver->restoreFailMessages());
    }

    public function testRestoreTimeoutMessages(): void
    {
        $driver = $this->getDriver();
        $driver->clear();

        $message = new Message();
        $message->setMessage('a');
        $messageId = $driver->push($message);
        $this->assertNotEmpty($messageId);

        $message = $this->getDriver()->pop();
        $this->assertNotEmpty($message->getMessageId());

        sleep(1);

        $message = $this->getDriver()->pop();
        $this->assertNull($message);

        $this->assertEquals(1, $driver->restoreTimeoutMessages());
    }
}
